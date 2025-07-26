<?php

namespace App\Service;

use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrService
{
    public function extractText(string $imagePath): ?string
    {
        try {
            return (new TesseractOCR($imagePath))
                ->lang('eng') // You can change this to 'rus' or 'ara' if needed
                ->run();
        } catch (\Throwable $e) {
            // Log the error if needed
            return null;
        }
    }
}
