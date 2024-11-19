<?php
include("connect.inp");
require("Mailer.php");
// require("QRcode.php");
$data = json_decode(file_get_contents("php://input"), true);

// $data = $data("")



function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
    ');';
    if ($with_script_tags) {
    $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
    }

    console_log('abcádfdsf', true);
if (isset($data['transaction_id'])) {
    console_log('đây', true);
    // echo 'Đã vào đây rồi';
    // Thêm bản ghi đặt phòng vào bảng booking_order
    $stmt = $con->prepare("INSERT INTO booking_order (room_id, check_in, check_out, booking_status, order_id) VALUES (?, ?, ?, ?, ?)");
    $room_id = $data['room_id']; // Room ID từ dữ liệu gửi tới
    $check_in = $data['checkin'];
    $check_out = $data['checkout'];
    $booking_status = $data['booking_status']; // Trạng thái đặt phòng
    $order_id = uniqid('ORD_'); // Tạo mã đơn đặt phòng duy nhất (VD: ORD_12345)
    
    $stmt->bind_param(
        'isssi',
        $room_id,
        $check_in,
        $check_out,
        $booking_status,
        $order_id
    );

    if ($stmt->execute()) {
        console_log('mail', true);
        // Dữ liệu lưu thành công, chuẩn bị gửi email
        $customer_name = $data['name'];
        $customer_email = $data['email'];
        $customer_phone = $data['phone'];
        $customer_address = $data['address'];
        $payment = $data['payment']; // Giá trị thanh toán (USD)
        
        $stmt = $con->prepare("SELECT room_name FROM rooms WHERE room_id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $room_res = $stmt->get_result();
        
        if ($room_res->num_rows > 0) {
            $room_data = $room_res->fetch_assoc();
            $room_name = $room_data['room_name']; // Lấy tên phòng
        } else {
            $room_name = 'Unknown Room'; // Giá trị mặc định nếu không tìm thấy phòng
        }
        
        // Gửi email xác nhận (ví dụ sử dụng PHPMailer)
        $mailer = new Mailer();
        $mail = $mailer->sendConfirmation($customer_email, $room_name, $check_in, $check_out, $order_id, $customer_name, $customer_phone, $customer_address, $payment);

    
       

        if ($mail) {
            http_response_code(200); // Thành công
            echo json_encode(['message' => 'Booking created and email sent successfully', 'order_id' => $order_id]);
        } 
    }

    $stmt->close();
} else {
    http_response_code(400); // Yêu cầu không hợp lệ
    echo json_encode(['error' => 'Invalid request']);
}

?>
