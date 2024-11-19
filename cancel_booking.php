<?php
include("connect.inp");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];

    // Cập nhật trạng thái booking thành 'pending'
    $update_query = "UPDATE booking_order SET booking_status = 'pending' WHERE booking_id = ?";
    $stmt = $con->prepare($update_query); // tạo một đối tượng câu lệnh chuẩn bị từ kết nối cơ sở dữ liệu $con
    $stmt->bind_param("i", $booking_id); //i: integer , liên kết biến $booking_id (một số nguyên) với tham số trong câu lệnh SQL đã chuẩn bị

    if ($stmt->execute()) {
        // Chuyển hướng quay lại trang đặt phòng sau khi hủy thành công
        header("Location: bookings.php");
        exit();
    } else {
        echo "Có lỗi xảy ra. Không thể hủy đặt phòng.";
    } 

    $stmt->close();
}

$con->close();
?>
