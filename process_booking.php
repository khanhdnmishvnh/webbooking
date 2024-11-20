<?php
include("connect.inp");
require("Mailer.php");

$data = json_decode(file_get_contents("php://input"), true);

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

if (isset($data['transaction_id'])) {
    console_log('Đang xử lý booking', true);

    // Bước 1: Thêm hoặc cập nhật thông tin khách hàng
    $customer_name = $data['name'];
    $customer_email = $data['email'];
    $customer_phone = $data['phone'];
    $customer_address = $data['address'];

    // Kiểm tra khách hàng đã tồn tại hay chưa (theo email hoặc số điện thoại)
    $stmt = $con->prepare("SELECT user_id FROM customer_details WHERE email = ? OR so_dien_thoai = ?");
    $stmt->bind_param('ss', $customer_email, $customer_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Khách hàng đã tồn tại, lấy user_id
        $customer_data = $result->fetch_assoc();
        $user_id = $customer_data['user_id'];
    } else {
        // Thêm khách hàng mới
        $stmt = $con->prepare("INSERT INTO customer_details (ho_ten, email, so_dien_thoai, dia_chi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $customer_name, $customer_email, $customer_phone, $customer_address);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id; // Lấy user_id vừa thêm
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save customer details']);
            exit();
        }
    }

    $stmt->close();
// Bước 3: Lưu thông tin thanh toán vào bảng payment_details
$trans_id = uniqid('TXN_'); // Tạo mã giao dịch duy nhất
$payment_status = 'Completed'; // Trạng thái thanh toán (hoàn thành)
$payment_method = 'PayPal'; // Phương thức thanh toán

$stmt = $con->prepare("INSERT INTO payment_details (booking_id, amount, payment_status, payment_method, trans_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param(
    'sdsss',
    $order_id,        // Mã đặt phòng (booking_id)
    $data['payment'], // Số tiền (USD)
    $payment_status,  // Trạng thái thanh toán
    $payment_method,  // Phương thức thanh toán
    $trans_id         // Mã giao dịch
);

if ($stmt->execute()) {
    // Thanh toán được lưu thành công
    console_log('Payment details saved successfully', true);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save payment details']);
    exit();
}

$stmt->close();
    // Bước 2: Thêm bản ghi đặt phòng vào bảng booking_order
    $stmt = $con->prepare("INSERT INTO booking_order (room_id, check_in, check_out, booking_status, order_id, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $room_id = $data['room_id'];
    $check_in = $data['checkin'];
    $check_out = $data['checkout'];
    $booking_status = $data['booking_status'];
    $order_id = uniqid('ORD_'); // Mã đặt phòng duy nhất

    $stmt->bind_param(
        'issssi',
        $room_id,
        $check_in,
        $check_out,
        $booking_status,
        $order_id,
        $user_id
    );

    if ($stmt->execute()) {
        // Booking và thông tin khách hàng đã được lưu
        console_log('Booking created successfully', true);

        // Gửi email xác nhận nếu cần thiết
        $mailer = new Mailer();
        $mail = $mailer->sendConfirmation($customer_email, $room_name, $check_in, $check_out, $order_id, $customer_name, $customer_phone, $customer_address, $data['payment']);

        if ($mail) {
            http_response_code(200);
            echo json_encode(['message' => 'Booking created and email sent successfully', 'order_id' => $order_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send confirmation email']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save booking details']);
    }

    $stmt->close();
} else {
    http_response_code(400); // Yêu cầu không hợp lệ
    echo json_encode(['error' => 'Invalid request']);
}
?>
