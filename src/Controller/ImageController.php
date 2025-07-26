<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Service\OcrService;
use App\Service\KeywordExtractor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ImageController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('image/index.html.twig');
    }

    #[Route('/upload', name: 'image_upload')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function upload(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        OcrService $ocrService,
        KeywordExtractor $keywordExtractor
    ): Response {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageFile')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                try {
                    $uploadPath = $this->getParameter('kernel.project_dir') . '/public/uploads';
                    $file->move($uploadPath, $newFilename);
                } catch (FileException $e) {
                    throw new \Exception("File upload failed: " . $e->getMessage());
                }

                $image->setFilename($newFilename);
                $image->setUser($this->getUser());

                // OCR + keywords
                $fullPath = $uploadPath . '/' . $newFilename;
                $ocrText = $ocrService->extractText($fullPath);
                $keywords = $keywordExtractor->extract($ocrText ?? '');

                $image->setExtractedText($ocrText);
                $image->setKeywords($keywords);

                $em->persist($image);
                $em->flush();

                $this->addFlash('success', 'Image uploaded and processed successfully.');

                return $this->redirectToRoute('my_images');
            }
        }

        return $this->render('image/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/my-images', name: 'my_images')]
    public function myImages(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        $search = $request->query->get('q');
        $search = $search ? mb_strtolower(trim($search)) : null;

        $images = $em->getRepository(Image::class)->findBy(['user' => $user]);

        if ($search) {
            $images = array_filter($images, function (Image $image) use ($search) {
                $textMatch = str_contains(mb_strtolower($image->getExtractedText() ?? ''), $search);
                $keywordMatch = in_array($search, array_map('mb_strtolower', $image->getKeywords() ?? []));
                return $textMatch || $keywordMatch;
            });
        }

        return $this->render('image/my_images.html.twig', [
            'images' => $images,
            'search' => $search
        ]);
    }

    #[Route('/public-images', name: 'public_images')]
    public function publicImages(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('q');
        $search = $search ? mb_strtolower(trim($search)) : null;

        $images = $em->getRepository(Image::class)->findBy(['visibility' => 'public']);

        if ($search) {
            $images = array_filter($images, function (Image $image) use ($search) {
                $textMatch = str_contains(mb_strtolower($image->getExtractedText() ?? ''), $search);
                $keywordMatch = in_array($search, array_map('mb_strtolower', $image->getKeywords() ?? []));
                return $textMatch || $keywordMatch;
            });
        }

        return $this->render('image/public_images.html.twig', [
            'images' => $images,
            'search' => $search
        ]);
    }
}
