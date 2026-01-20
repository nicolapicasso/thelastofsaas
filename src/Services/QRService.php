<?php

namespace App\Services;

/**
 * QR Code Service
 * TLOS - The Last of SaaS
 *
 * Generates QR codes for tickets and live matching
 */
class QRService
{
    private int $size;
    private int $margin;
    private string $errorCorrection;

    public function __construct(int $size = 300, int $margin = 2, string $errorCorrection = 'M')
    {
        $this->size = $size;
        $this->margin = $margin;
        $this->errorCorrection = $errorCorrection; // L, M, Q, H
    }

    /**
     * Generate QR code as PNG binary
     */
    public function generatePng(string $data): string
    {
        // Try QR Server API (free, reliable)
        $url = $this->buildQRServerUrl($data);

        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        $imageData = @file_get_contents($url, false, $context);

        if ($imageData && strlen($imageData) > 100) {
            return $imageData;
        }

        // Fallback: generate a simple placeholder
        return $this->generatePlaceholder($data);
    }

    /**
     * Build QR Server API URL (https://goqr.me/api/)
     */
    private function buildQRServerUrl(string $data): string
    {
        $params = [
            'size' => $this->size . 'x' . $this->size,
            'data' => $data,
            'margin' => $this->margin,
            'ecc' => $this->errorCorrection
        ];

        return 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query($params);
    }

    /**
     * Generate QR code as base64 data URI
     */
    public function generateDataUri(string $data): string
    {
        $png = $this->generatePng($data);
        return 'data:image/png;base64,' . base64_encode($png);
    }

    /**
     * Generate QR code and save to file
     */
    public function saveToFile(string $data, string $filePath): bool
    {
        $png = $this->generatePng($data);

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents($filePath, $png) !== false;
    }

    /**
     * Generate QR code for ticket
     */
    public function generateTicketQR(string $ticketCode): string
    {
        return $this->generateDataUri($ticketCode);
    }

    /**
     * Generate QR code for live matching (with URL)
     */
    public function generateMatchingQR(string $code, string $baseUrl): string
    {
        $url = rtrim($baseUrl, '/') . '/live/' . $code;
        return $this->generateDataUri($url);
    }

    /**
     * Build Google Charts API URL
     * Note: This API is deprecated but still functional
     * For production, use a proper QR library
     */
    private function buildGoogleChartsUrl(string $data): string
    {
        $ecl = match($this->errorCorrection) {
            'L' => 'L',
            'M' => 'M',
            'Q' => 'Q',
            'H' => 'H',
            default => 'M'
        };

        $params = [
            'cht' => 'qr',
            'chs' => $this->size . 'x' . $this->size,
            'chl' => $data,
            'choe' => 'UTF-8',
            'chld' => $ecl . '|' . $this->margin
        ];

        return 'https://chart.googleapis.com/chart?' . http_build_query($params);
    }

    /**
     * Generate a simple placeholder QR (when API fails)
     */
    private function generatePlaceholder(string $data): string
    {
        // Create a simple image with text
        $image = imagecreatetruecolor($this->size, $this->size);

        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 200, 200, 200);

        // Fill background
        imagefill($image, 0, 0, $white);

        // Draw border
        imagerectangle($image, 0, 0, $this->size - 1, $this->size - 1, $gray);

        // Add text
        $fontSize = 3;
        $text = substr($data, 0, 20);
        $textWidth = strlen($text) * imagefontwidth($fontSize);
        $x = (int)(($this->size - $textWidth) / 2);
        $y = (int)(($this->size - imagefontheight($fontSize)) / 2);

        imagestring($image, $fontSize, $x, $y - 20, 'QR Code', $black);
        imagestring($image, $fontSize, $x, $y + 10, $text, $black);

        // Get PNG data
        ob_start();
        imagepng($image);
        $pngData = ob_get_clean();

        imagedestroy($image);

        return $pngData;
    }

    /**
     * Decode QR code from image (requires external service or library)
     */
    public function decode(string $imagePath): ?string
    {
        // For QR code decoding, you would typically use:
        // - zxing library
        // - A web API service
        // - PHP extension

        // Placeholder implementation
        // In production, implement actual decoding

        return null;
    }

    /**
     * Validate QR code format
     */
    public function isValidTicketCode(string $code): bool
    {
        // TLOS ticket codes are 12 characters alphanumeric
        return preg_match('/^[A-Z0-9]{12}$/', $code) === 1;
    }

    /**
     * Generate a unique code for tickets/entities
     */
    public static function generateCode(int $length = 12): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $code;
    }
}
