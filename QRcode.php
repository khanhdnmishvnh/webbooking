<?php
require_once __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QRGenerator {
    public function generate($orderId, $customerData) {
        // URL mà mã QR sẽ dẫn đến
        // $url = "https://www.facebook.com/oanh.lui.5?orderId=" . urlencode($bookingId);
        $text = "Order ID: " . $orderId . "\n";
        foreach ($customerData as $key => $value) {
            $text .= ucfirst($key) . ": " . $value . "\n";
        }
        // Tạo QR code
        $qrCode = QrCode::create($text)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $qrPath = __DIR__ . "/qrcodes/QR_" . $orderId . ".png";

        // Ghi QR code ra file
        $writer->write($qrCode)->saveToFile($qrPath);

        return $qrPath;
    }
}
