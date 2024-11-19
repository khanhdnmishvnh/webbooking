<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>
    <title>CONFIRM BOOKING</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body class="bg-light">
    <?php require('inc/header.php');?>
    <?php require('connect.inp');?>
    <?php
      function filteration($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = htmlspecialchars(strip_tags(trim($value)));
            }
        } else {
            $data = htmlspecialchars(strip_tags(trim($data)));
        }
        return $data;
    }

    function redirect($url) {
        header("Location: $url");
        exit();
    }
    if(!isset($_GET['id'])){
        redirect('rooms.php'); 
    }


    $room_id = filteration($_GET['id']);
    include("connect.inp");

    $stmt = $con->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room_res = $stmt->get_result();

    if ($room_res->num_rows == 0) {
        header('Location: rooms.php');
        exit;
    }

    $room_data = $room_res->fetch_assoc();
    $room_price = $room_data['price'];
    $room_thumb = $room_data['image_url'] ? $room_data['image_url'] : "default.jpg";
    ?>

    <div class="container">
     <div class="row">
        <div class="col-12 my-5 mb-4 px-4">
            <h2 class="fw-bold"> CONFIRM BOOKING</h2>
        </div>
        <div class="col-lg-7 col-md-12 px-4">
            <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                <img src="images/<?php echo $room_thumb ?>" class="img-fluid rounded">
                </div>    
            </div>
            <h5><?php echo $room_data['room_name']; ?></h5>
            <h6 class="mb-4" id="price"><?php echo $room_data['price']; ?></h6>
        </div>
        <div class="col-lg-5 col-md-12 px-4">
            <div class="card mb-4 border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <form action="#" method="POST" id="booking_form">
                        <h6 class="mb-3">BOOKING DETAILS</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input name="name" type="text" class="form-control shadow-none" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">PhoneNumber</label>
                                <input name="phonenum" type="number" class="form-control shadow-none" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Address</label>
                                <input name="address" type="text" class="form-control shadow-none" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Email</label>
                                <input name="email" type="text" class="form-control shadow-none" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-in</label>
                                <input name="checkin" type="date" class="form-control shadow-none" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Check-out</label>
                                <input name="checkout" type="date" class="form-control shadow-none" required>
                            </div>

                            <div class="col-12">
                                <div class="spinner-border text-info mb-3 d-none" id="info_loader" role="status">
                                    <span class="visually-hidden">Loading.....</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <h6 class="mb-3 text-danger" id="pay_info">Vui lòng nhập ngày checkin và checkout!</h6>
                                <div class="pay_now d-none" id="pay_now">
                                <div id="paypal-button-container"></div>
                                </div>  
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>


    <?php
    // Kết nối cơ sở dữ liệu và kiểm tra phòng
    $room_id = filteration($_GET['id']);
    include("connect.inp");
    $booked_dates = [];
    $stmt = $con->prepare("SELECT check_in, check_out FROM booking_order WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $booked_dates[] = ['checkin' => $row['check_in'], 'checkout' => $row['check_out']];
    }
    $stmt->close();
    ?>
    <script>
         const bookedDates = <?php echo json_encode($booked_dates); ?>;
    </script>

<script>
function date_diff(startDate, endDate) {
    const diffTime = Math.abs(endDate - startDate);
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}
let payment_usd = 0;
function validateDates() {
    let checkin = booking_form.elements['checkin'].value;
    let checkout = booking_form.elements['checkout'].value;
    let checkinDate = new Date(checkin);
    let checkoutDate = new Date(checkout);
    let today = new Date();
    today.setHours(0, 0, 0, 0);

    if (checkinDate < today) {
        pay_info.textContent = "Ngày check-in không thể là ngày trong quá khứ!";
        return false;
    }

    if (checkoutDate <= checkinDate) {
        pay_info.textContent = "Ngày check-out không thể nhỏ hơn ngày check-in!";
        return false;
    }


    for (let booking of bookedDates) {
        let bookedCheckin = new Date(booking.checkin);
        let bookedCheckout = new Date(booking.checkout);

        if ((checkinDate < bookedCheckout) && (checkoutDate > bookedCheckin)) {
            pay_info.textContent = "Ngày bạn chọn trùng với một khoảng thời gian đã đặt!";
            return false;
        }
    }

    let count_days = date_diff(checkinDate, checkoutDate);
    let price = parseFloat(document.getElementById('price').textContent);
    let payment_vnd = price * count_days;
    const usd = 23000;
    payment_usd = (payment_vnd / usd).toFixed(2);

    pay_info.innerHTML = "Total days: " + count_days + "<br>Total payment: " + payment_vnd.toFixed(2) + " VND = " + payment_usd + "$";

    return true;
}

function validateForm() {
    let requiredFields = ['name', 'phonenum', 'address', 'email', 'checkin', 'checkout'];
    let allFilled = requiredFields.every(field => booking_form.elements[field].value.trim() !== '');

    if (allFilled && validateDates()) {
        document.getElementById('pay_now').classList.remove('d-none');
    } else {
        document.getElementById('pay_now').classList.add('d-none');
    }
}

document.querySelectorAll('#booking_form input').forEach(input => {
    input.addEventListener('input', validateForm);
});
</script>

<?php require('inc/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://www.paypal.com/sdk/js?client-id=AcoMtjzoZmvuuhRNzeUtpg7OsWSJFa2L6x78oI0-heWO1vj_IpgVcK4O35nOOWw2jKZr38PyCUW7qJU5&buyer-country=US&currency=USD&components=buttons&enable-funding=venmo,paylater,card" data-sdk-integration-source="developer-studio"></script>
<script>paypal.Buttons({
    style: {
        layout: 'vertical',
        color: 'blue',
        shape: 'pill',
        label: 'pay'
    },
    createOrder: function(data, actions) {
        // Tạo order với PayPal
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: payment_usd // Số tiền thanh toán (dạng USD)
                },
                description: "Room booking payment",
            }]
        });
    },
    onApprove: function(data, actions) {
        // Xử lý khi thanh toán thành công
        return actions.order.capture().then(function(details) {
            console.log("detail",details);
            alert('Payment completed successfully by ' + details.payer.name.given_name);
            
            // Thu thập dữ liệu khách hàng từ form
            const customerData = {
                transaction_id: details.id, // ID giao dịch từ PayPal
                room_id: <?php echo json_encode($room_id); ?>, // Room ID từ PHP
                checkin: booking_form.elements['checkin'].value, // Ngày nhận phòng
                checkout: booking_form.elements['checkout'].value, // Ngày trả phòng
                payment: payment_usd, // Số tiền thanh toán (USD)
                booking_status: 'Confirmed', // Trạng thái đặt phòng
                // Thông tin khách hàng
                name: booking_form.elements['name'].value,
                phone: booking_form.elements['phonenum'].value,
                address: booking_form.elements['address'].value,
                email: booking_form.elements['email'].value
            };

            // Gửi dữ liệu đặt phòng đến máy chủ để xử lý
            fetch('process_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(customerData)
            }).then(response => {
                if (response.ok) {
                    window.location.href = 'thank_you.php'; // Điều hướng đến trang cảm ơn
                } else {
                    alert("Failed to save booking details. Please contact support.");
                }
            });
        });
    },
    onError: function(err) {
        alert('An error occurred during payment: ' + err);
    }
}).render('#paypal-button-container'); // Hiển thị nút thanh toán tại phần tử này

</script>
</body>
</html>
