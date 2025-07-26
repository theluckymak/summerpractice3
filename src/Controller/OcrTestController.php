<?php

namespace App\Controller;

use App\Service\OcrService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OcrTestController extends AbstractController
{
    #[Route('/test-ocr', name: 'test_ocr')]
    public function test(OcrService $ocrService): Response
    {
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/test-image.png';

        if (!file_exists($imagePath)) {
            return new Response('Image not found: ' . $imagePath);
        }

        $text = $ocrService->extractText($imagePath);

        return new Response('<pre>' . htmlspecialchars($text ?? 'No text extracted.') . '</pre>');
    }
}
