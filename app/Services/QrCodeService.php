<?php

namespace App\Services;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    public function png(string $value): string
    {
        $writer = new PngWriter;
        $qrCode = new QrCode(
            data: $value,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 300,
            margin: 10,
        );

        return $writer->write($qrCode)->getString();
    }

    public function dataUri(string $value): string
    {
        return 'data:image/png;base64,'.base64_encode($this->png($value));
    }
}
