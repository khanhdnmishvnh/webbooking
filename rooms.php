<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROOMS</title>
    <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">
    <?php
    require('inc/header.php');
    
    ?>
   <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">OUR ROOMS</h2>
    <div class="h-line bg-dark"></div>

   </div> 
   <div class="containerfluid">
    <div class="row">
        <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 ps-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
                <div class="container-fluid flex-lg-culumn align-items-stretch">
                  <h4 class="mt-2 mb-3">FILTERS</h4>
                 
                  <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filterDropdown">
                   <div class="border bg-light p-3 rounded mb-3">
                    <h5 class="mb-3" style="font-size: 18px;">CHECK AVAILABILITY</h5>
                    <label class="form-label">Check-in</label>
                    <input type="date" class="form-control shadow-none mb-3">
                    <label class="form-label">Check-out</label>
                    <input type="date" class="form-control shadow-none">
                   </div>
                   <div class="border bg-light p-3 rounded mb-3">
                    <h5 class="mb-3" style="font-size: 18px;">FACILITIES</h5>
                    <div class="mb-2">
                    <input type="checkbox" id="f1" class="form-check-input shadow-none me-1">
                    <label class="form-check-label" for="f1">Facility one</label>
                    </div>
                    <div class="mb-2">
                    <input type="checkbox" id="f2" class="form-check-input shadow-none me-1">
                    <label class="form-check-label" for="f2">Facility one</label>
                    </div>
                    <div class="mb-2">
                    <input type="checkbox" id="f3" class="form-check-input shadow-none me-1">
                    <label class="form-check-label" for="f3">Facility one</label>
                    </div>
                   </div>
                   <div class="border bg-light p-3 rounded mb-3">
                    <h5 class="mb-3" style="font-size: 18px;">GUESTS</h5>
                    <div class="d-flex">
                        <div class="me-3">
                            <label class="form-label">Adults</label>  
                            <input type="number" class="form-control shadow-none">
                        </div>
                        <div>
                            <label class="form-label">Children</label>  
                            <input type="number" class="form-control shadow-none">
                        </div>
                    </div>     
                   </div>
                  </div>
                </div>
              </nav>
        </div>

        <div class="col-lg-9 col-md-12 px-4">

        <?php
        include("connect.inp");
        $room_res = mysqli_query($con,"SELECT * FROM `rooms` WHERE status = 'available'");
        

        while($room_data = mysqli_fetch_assoc($room_res))
        {
           //get facilities of room
           $fac_q = mysqli_query($con,"SELECT f.facility_name FROM `facilities` f
            INNER JOIN room_facilities rfac ON f.facility_id=rfac.facility_id
             WHERE rfac.room_id = '{$room_data['room_id']}'");
           $facilities_data = "";
           while($fac_row = mysqli_fetch_assoc($fac_q)){
            $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap'>
            $fac_row[facility_name]
            </span>";
           }
           //get images
           
           $room_thumb = $room_data['image_url'] ? $room_data['image_url'] : "default.jpg";
        // print room card 
        echo <<<data
            <div class="card mb-4 border-0 shadow">
                <div class="row g-0 p-3 align-items-center">
                    <div class="col-md-5 mb-lg-0 mb-md-0 mb-3">
                    <img src="images/$room_thumb" class="img-fluid rounded">
                    </div>
                    <div class="col-md-5 px-lg-3 px-md-3 px-0">
                        <h5 class="mb-3">$room_data[room_name]</h5>
                        <div class="features mb-3">
                            <h6 class="mb-1">Mô tả</h6>
                           $room_data[description]
                        </div>
                        <div class="facilities mb-3">
                            <h6 class="mb-1">Cơ sở vật chất</h6>
                            $facilities_data
                        </div>
                        <div class="guests">
                        <h6 class="mb-1">Dành cho</h6>
                        <span class="badge rounded-pill bg-light text-dark text-wrap">
                        $room_data[max_adults] Người lớn
                        </span>
                        <span class="badge rounded-pill bg-light text-dark text-wrap">
                        $room_data[max_children] Trẻ em
                        </span>
                        </div>

                    </div>
                    <div class="col-md-2 mt-lg-0 mt-md-0 mt-4 text-center">
                        <h6 class="mb-4">$room_data[price]VND</h6>
                        <a href="" class="btn btn-sm w-100 text-white custom-bg shadow-none mb-2">Đặt ngay</a>
                        <a href="room_details.php?id=$room_data[room_id]" class="btn btn-sm w-100 btn-outline-dark shadow-none">Chi tiết</a>
                    
                    </div>
                </div>
            </div>



        data;

        }
        ?>

        </div>
    </div>
   </div>
   <?php require('inc/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  
</body>
</html>
