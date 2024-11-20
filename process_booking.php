<?php
include("connect.inp");
require("Mailer.php");
require("QRcode.php");

$data = json_decode(file_get_contents("php://input"), true);

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

if ($data) {
    console_log('Đang xử lý booking', true);

    // Kiểm tra kết nối
    if (!$con) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }

    // 1. Thêm hoặc lấy thông tin khách hàng
    $customer_email = $data['email'];
    $customer_phone = $data['phone'];
    $stmt = $con->prepare("SELECT user_id FROM customer_details WHERE email = ? OR so_dien_thoai = ?");
    $stmt->bind_param('ss', $customer_email, $customer_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_id = $result->fetch_assoc()['user_id'];
    } else {
        $stmt = $con->prepare("INSERT INTO customer_details (ho_ten, email, so_dien_thoai, dia_chi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $data['name'], $customer_email, $customer_phone, $data['address']);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save customer details']);
            exit();
        }
    }
    $stmt->close();

    // 2. Thêm thông tin booking
    $stmt = $con->prepare("INSERT INTO booking_order (room_id, check_in, check_out, booking_status, order_id, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $room_id = $data['room_id'];
    $check_in = $data['checkin'];
    $check_out = $data['checkout'];
    $booking_status = $data['booking_status'];
    $order_id = uniqid('ORD_');

    $stmt->bind_param('issssi', $room_id, $check_in, $check_out, $booking_status, $order_id, $user_id);
    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save booking details']);
        exit();
    }
    $stmt->close();

    // 3. Thêm thông tin thanh toán
    $stmt = $con->prepare("INSERT INTO payment_details (booking_id, amount, payment_status, payment_method, trans_id, payment_date) VALUES (?, ?, ?, ?, ?, ?)");
    $trans_id = uniqid('TXN_');
    $payment_status = 'Completed';
    $payment_method = 'PayPal';
    $payment_date = date('Y-m-d H:i:s');
    $stmt->bind_param('sdssss', $booking_id, $data['payment'], $payment_status, $payment_method, $trans_id, $payment_date);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save payment details']);
        exit();
    }
    $stmt->close();

    // 4. Tạo QR code và gửi email
    $qrCode = new QRGenerator();
    $qrPath = $qrCode->generate($order_id, $data);
    $mailer = new Mailer();
    $mail = $mailer->sendConfirmation($customer_email, $data['room_name'], $check_in, $check_out, $order_id, $data['name'], $data['phone'], $data['address'], $data['payment'], $qrPath, _DIR_ . "/images/default.jpg");

    if ($mail) {
        http_response_code(200);
        echo json_encode(['message' => 'Booking created and email sent successfully', 'order_id' => $order_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to send confirmation email']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>