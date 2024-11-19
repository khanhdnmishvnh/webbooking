<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>
    <title>Thank You</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
        }
        footer {
            background-color: #f8f9fa;
            padding: 20px 0;
        }
    </style>
</head>
<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <main class="container text-center my-5">
        <h1 class="display-4 fw-bold text-success">Thank You!</h1>
        <p class="lead">Your booking has been successfully completed.</p>
        <iframe src="https://giphy.com/embed/uWlpPGquhGZNFzY90z" width="480" height="350" style="" frameBorder="0" class="giphy-embed" allowFullScreen></iframe><p><a href="https://giphy.com/gifs/moodman-thank-you-funny-uWlpPGquhGZNFzY90z"></a></p>
        <p>We have sent the booking details to your email. If you have any questions, please feel free to contact our support team.</p>
        <a href="index.php" class="btn btn-primary mt-4">Back to Home</a>
    </main>

    <?php require('inc/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsXs1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
