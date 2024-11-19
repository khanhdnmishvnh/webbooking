<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel</title>
    <?php
    require('inc/links.php');
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
   <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
      .availability-form{
        margin-top:-50px;
        z-index: 11;
        position: relative;
        }
      @media screen and (max-width: 575px){
        .availability-form{
        margin-top:25px;
       padding: 0 35px;
        }
      }


    </style>
</head>
<body class="bg-light">
  <!-- Header -->
<?php
require('inc/header.php');
?>



<!-- check availability form -->
 <div class="container">
  <div class="row">
    <div class="col-lg-12 bg-white shadow p-4 rounded">
     <h5 class="mb-4"></h5> 
     <form method="GET" action="" id="check" onsubmit="return validateDates();" >
    <div class="row align-items-end">
        <div class="col-lg-3 mb-3">
            <label class="form-label" id ="check" style="font-weight:500;">Check-in</label>
            <input type="date" name="checkin_date" class="form-control shadow-none">
        </div>
        <div class="col-lg-3 mb-3">
            <label class="form-label"id ="check" style="font-weight:500;">Check-out</label>
            <input type="date" name="checkout_date" class="form-control shadow-none">
        </div>
        <div class="col-lg-3 mb-3">
            <label class="form-label"id ="check" style="font-weight:500;">Adult</label>
            <select name="adult_count" class="form-select shadow-none">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="col-lg-2 mb-3">
            <label class="form-label"id ="check" style="font-weight:500;">Children</label>
            <select name="children_count" class="form-select shadow-none">
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>
        </div>
        <div class="col-lg-1 mb-lg-3 mt-2">
            <button type="submit" class="btn text-white shadow-none custom-bg">Submit</button>
        </div>
        <h6 class="mb-3 text-danger" id="pay_info"></h6>
    </div>
</form>
    </div>
  </div>
</div> 




<!-- Our Rooms -->
 <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR ROOMS</h2>
 <div class="container">
  <div class="row">
  
  <?php
include("connect.inp");

// Truy xuất các giá trị bộ lọc từ URL (GET) và đặt điều kiện mặc định
$checkin_date = isset($_GET['checkin_date']) ? $_GET['checkin_date'] : '';
$checkout_date = isset($_GET['checkout_date']) ? $_GET['checkout_date'] : '';
$adult_count = isset($_GET['adult_count']) ? $_GET['adult_count'] : 1;
$children_count = isset($_GET['children_count']) ? $_GET['children_count'] : 0;


// Build the SQL query with filters
$sql = "SELECT * FROM `rooms` WHERE status = 'available'";

// Add conditions based on filters
if ($checkin_date && $checkout_date) {
    $sql .= " AND NOT EXISTS (
                SELECT 1 FROM `booking_order`
                WHERE rooms.room_id = booking_order.room_id
                AND ('$checkin_date' BETWEEN booking_order.check_in AND booking_order.check_out
                OR '$checkout_date' BETWEEN booking_order.check_in AND booking_order.check_out)
              )";
}
if ($adult_count) {
    $sql .= " AND max_adults >= $adult_count";  // Updated column name
}
if ($children_count) {
    $sql .= " AND max_children >= $children_count";  // Updated column name
}

// Truy vấn và xử lí lỗi
$room_res = mysqli_query($con, $sql);

if (!$room_res) {
    die("Query failed: " . mysqli_error($con));
}

// Trình bày kết quả
while ($room_data = mysqli_fetch_assoc($room_res)) {
    $fac_q = mysqli_query($con, "SELECT f.facility_name FROM `facilities` f
                    INNER JOIN room_facilities rfac ON f.facility_id=rfac.facility_id
                    WHERE rfac.room_id = '{$room_data['room_id']}'");
    $facilities_data = "";
    while ($fac_row = mysqli_fetch_assoc($fac_q)) {
        $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap'>$fac_row[facility_name]</span>";
    }
    $room_thumb = $room_data['image_url'] ? $room_data['image_url'] : "default.jpg";

    // Hiển thị room
    echo <<<data
    <div class="col-lg-4 col-md-6 my-3">
        <div class="card border-0 shadow" style="max-width: 350px; margin: auto;">
            <img src="images/$room_thumb" class="card-img-top">
            <div class="card-body">
                <h5>$room_data[room_name]</h5>
                <h6 class="mb-4">$room_data[price] VND</h6>
                <div class="features mb-4">
                    <h6 class="mb-1">Mô tả</h6>
                    $room_data[description]
                </div>
                <div class="facilities mb-4">
                    <h6 class="mb-1">Cơ sở vật chất</h6>
                    $facilities_data
                </div>
                <div class="guests mb-4">
                  <h6 class="mb-1">Dành cho</h6>
                    <span class="badge rounded-pill bg-light text-dark text-wrap">
                      $room_data[max_adults] Người lớn
                      </span>
                      <span class="badge rounded-pill bg-light text-dark text-wrap">
                      $room_data[max_children] Trẻ em
                      </span>
                </div>
                <div class="d-flex justify-content-evenly">
                    <a href="confirm_booking.php?id=$room_data[room_id]" class="btn btn-sm text-white custom-bg shadow-none">Book Now</a>
                    <a href="room_details.php?id=$room_data[room_id]" class="btn btn-sm btn-outline-dark shadow-none">Details</a>
                </div>
            </div>
        </div>
    </div>
    data;
}
?>





      
    <div class="col-lg-12 text-center mt-5">
      <a href="#" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Rooms</a>
    </div>
  </div>
 </div>

 <!-- Footer -->
 <?php
 require('inc/footer.php');
 ?>



// kiểm tra ngày nhập vào

<script>
function date_diff(startDate, endDate) {
    const diffTime = Math.abs(endDate - startDate);
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

function validateDates() {
    // Lấy giá trị từ các trường input của form
    let check = document.getElementById('check');
    let checkin = check.elements['checkin_date'].value;
    let checkout = check.elements['checkout_date'].value;
    
    // Chuyển đổi giá trị thành đối tượng Date
    let checkinDate = new Date(checkin);
    let checkoutDate = new Date(checkout);
    let today = new Date();
    today.setHours(0, 0, 0, 0); // Đặt giờ về 0 để chỉ so sánh ngày

    // Thông báo lỗi sẽ được hiển thị tại thẻ có id "pay_info"
    let pay_info = document.getElementById("pay_info");

    // Kiểm tra ngày check-in không được là ngày trong quá khứ
    if (checkinDate < today) {
        pay_info.textContent = "Ngày check-in không thể là ngày trong quá khứ!";
        return false;
    }

    // Kiểm tra ngày check-out không thể nhỏ hơn hoặc bằng ngày check-in
    if (checkoutDate <= checkinDate) {
        pay_info.textContent = "Ngày check-out không thể nhỏ hơn ngày check-in!";
        return false;
    }

    // Xóa thông báo lỗi nếu mọi thứ hợp lệ
    pay_info.textContent = "";
    return true; // Cho phép form gửi nếu không có lỗi
}
</script>

<script
src="https://www.paypal.com/sdk/js?client-id=AcRFoe-qt7M7cdr5naUgz1mUGNZkjehzrqzTLh0tYsK-syVpAVkI3lLRkhHC-xhtU0ZpgXMdC68J0m6A&buyer-country=US&currency=USD&components=buttons&enable-funding=card&disable-funding=venmo,paylater"
data-sdk-integration-source="developer-studio"
></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>