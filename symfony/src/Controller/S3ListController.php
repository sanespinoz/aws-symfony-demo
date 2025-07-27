<?php

namespace App\Controller;

use App\Service\S3Uploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class S3ListController extends AbstractController
{
    #[Route('/s3-files', name: 's3_files')]
    public function list(S3Uploader $s3Uploader): Response
    {
        $archivos = $s3Uploader->listFiles();

        return $this->render('s3/list.html.twig', [
            'archivos' => $archivos,
        ]);
    }
}
