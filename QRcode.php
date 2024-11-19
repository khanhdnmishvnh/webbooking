<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Writer\PngWriter;

class QRGenerator {
    public function generate($orderId) {
        // URL mà mã QR sẽ dẫn đến
        $url = "https://www.facebook.com/oanh.lui.5?orderId=" . urlencode($orderId);

        // Đường dẫn lưu file QR code
        $qrPath = __DIR__ . "/qrcodes/QR_" . $orderId . ".png";

        // Tạo QR code
        $result = Builder::create()
            ->writer(new PngWriter()) // Định dạng PNG
            ->data($url) // Dữ liệu mã QR
            ->encoding(new Encoding('UTF-8')) // Mã hóa UTF-8
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh()) // Mức sửa lỗi cao
            ->size(300) // Kích thước
            ->margin(10) // Lề
            ->build();

        // Lưu QR code vào file
        $result->saveToFile($qrPath);

        // Trả về đường dẫn QR code
        return $qrPath;
    }
}
