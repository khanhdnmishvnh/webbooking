<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// use Dotenv\Dotenv;

class Mailer {
    public function __construct() {
        // $dotenv = Dotenv::createImmutable(__DIR__ . '/');
        // $dotenv->load();
    }

    public function sendConfirmation($email, $roomName, $checkIn, $checkOut, $orderId, $name, $phone, $address, $amount) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "dongockhanh2k3@gmail.com";
            $mail->Password = "gwai ontb isbf qexo";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom("dongockhanh2k3@gmail.com", 'PaHoPaHVNH');
            $mail->addAddress($email);
    
            // Soạn thảo nội dung email bao gồm thông tin đặt phòng
            // Soạn thảo nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Booking Confirmation';

            // Tạo nội dung email với thông tin đặt phòng
            $mailContent = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                        background-color: #f9f9f9;
                        margin: 0;
                        padding: 0;
                    }
                    .email-container {
                        max-width: 600px;
                        margin: 20px auto;
                        background: #ffffff;
                        border: 1px solid #dddddd;
                        border-radius: 8px;
                        overflow: hidden;
                    }
                    .header {
                        background: #4CAF50;
                        color: #ffffff;
                        padding: 20px;
                        text-align: center;
                    }
                    .header h2 {
                        margin: 0;
                    }
                    .content {
                        padding: 20px;
                    }
                    .content p {
                        margin: 10px 0;
                    }
                    .details ul {
                        list-style: none;
                        padding: 0;
                    }
                    .details ul li {
                        margin: 5px 0;
                        font-size: 14px;
                    }
                    .details ul li strong {
                        color: #4CAF50;
                    }
                    .footer {
                        background: #f1f1f1;
                        color: #666;
                        text-align: center;
                        padding: 10px;
                        font-size: 12px;
                    }
                    .cta-button {
                        display: inline-block;
                        background: #4CAF50;
                        color: #fff;
                        text-decoration: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                        margin: 20px 0;
                        font-weight: bold;
                    }
                    .cta-button:hover {
                        background: #45a049;
                    }
                </style>
            </head>
            <body>
                <div class="email-container">
                    <div class="header">
                        <h2>Thank You for Your Booking!</h2>
                    </div>
                    <div class="content">
                        <p>Dear ' . htmlspecialchars($name) . ',</p>
                        <p>We are excited to confirm your booking. Below are the details:</p>
                        <div class="details">
                            <ul>
                                <li><strong>Order ID:</strong> ' . htmlspecialchars($orderId) . '</li>
                                <li><strong>Room Name:</strong> ' . htmlspecialchars($roomName) . '</li>
                                <li><strong>Check-in Date:</strong> ' . htmlspecialchars($checkIn) . '</li>
                                <li><strong>Check-out Date:</strong> ' . htmlspecialchars($checkOut) . '</li>
                                <li><strong>Customer Name:</strong> ' . htmlspecialchars($name) . '</li>
                                <li><strong>Phone:</strong> ' . htmlspecialchars($phone) . '</li>
                                <li><strong>Address:</strong> ' . htmlspecialchars($address) . '</li>
                                <li><strong>Total Payment:</strong> $' . htmlspecialchars($amount) . '</li>
                            </ul>
                        </div>
                        <p>Please find the QR code attached for a smoother check-in experience.</p>
                
                    </div>
                    <div class="footer">
                        <p>Thank you for choosing PaHoPaHVNH. We look forward to welcoming you!</p>
                        <p>&copy; 2024 PaHoPaHVNH. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>';

            $mail->Body = $mailContent;

            // // Đính kèm mã QR (nếu có)
            // if (!empty($qrPath)) {
            //     $mail->addAttachment($qrPath);
            // }

            // Gửi email
            return $mail->send();      
            
            // Đính kèm mã QR
            // $mail->addAttachment($qrCodePath);

        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }    
}
