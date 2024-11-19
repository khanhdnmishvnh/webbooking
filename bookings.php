<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>
    <title><?php echo $settings_r['site_title'] ?> -BOOKINGS</title>
</head>
<body class="bg-light">
    <?php
    require('inc/header.php'); 
    //3p11: login các kiểu
    ?>
 
 <div class="container">
    <div class="row">

    <div class="col-12 my-5 px-4">
        <h2 class="fw-bold">BOOKINGS</h2>
        <div style="font-size:14px;">
            <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
             <span class="text-secondary">></span>
             <a href="#" class="text-secondary text-decoration-none">BOOKINGS</a>
        </div>
    </div>

    <?php
    
include("connect.inp");


// Truy vấn để lấy thông tin từ bảng booking_order và rooms
$query = "SELECT bo.*, r.room_name, r.price, r.image_url
          FROM booking_order AS bo 
          INNER JOIN rooms AS r ON bo.room_id = r.room_id
          WHERE bo.booking_status IN ('Confirmed', 'Pending', 'Cancelled')
          AND r.status = 'available'
          ORDER BY bo.booking_id DESC";


$result = $con->query($query);

if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {
        $date = date("d-m-Y", strtotime($data['check_in'])); // Ngày check-in để hiển thị
        $checkin = date("d-m-Y", strtotime($data['check_in']));
        $checkout = date("d-m-Y", strtotime($data['check_out']));

        // Xác định màu nền và trạng thái hiển thị
        $status_bg = "";
        $status_text = "";
        if ($data['booking_status'] == 'Confirmed') {
            $status_bg = "bg-success";
            $status_text = "booked";
        } else if ($data['booking_status'] == 'Cancelled') {
            $status_bg = "bg-danger";
            $status_text = "cancelled";
        } else {
            $status_bg = "bg-warning";
            $status_text = "pending";
        }

        echo <<<bookings
        <div class='col-md-4 px-4 mb-4'>
            <div class='bg-white p-3 rounded shadow-sm'>
                <!-- Hiển thị hình ảnh phòng -->
                <img src="images/{$data['image_url']}" alt="Room Image" class="img-fluid rounded mb-3" style="width: 100%; height: 111px; object-fit: cover;">             
                <h5 class='fw-bold'>{$data['room_name']}</h5>
                <p>{$data['price']} VND/day</p>
                <p>
                    <b>Check in:</b> $checkin <br>
                    <b>Check out:</b> $checkout
                </p>
                <p>
                    <b>Amount:</b> {$data['price']} VND <br>
                    <b>Order ID:</b> {$data['order_id']} <br>
                    <b>Date:</b> $date
                </p>
                <p>
                    <span class='badge $status_bg'>$status_text</span>
                </p>
                
bookings;
//HỦY ĐẶT PHÒNG
    if ($status_text == "booked") {
    echo <<<cancel
    <form action="cancel_booking.php" method="POST">
        <input type="hidden" name="booking_id" value="{$data['booking_id']}">
        <button type="submit" class="btn btn-sm btn-dark text-white mt-2" style="padding: 2px 12px; font-size: 11px; ">Cancel</button>

    </form>
    cancel;
    }


        echo "</div></div>";
    }
} else {
    echo "<p>Không có dữ liệu đặt phòng.</p>";
}

// Đóng kết nối
$con->close();
        ?>
            <?php require('inc/footer.php'); ?>
    
 </div>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</body>
</html>