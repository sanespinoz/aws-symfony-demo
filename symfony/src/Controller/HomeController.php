<?php

namespace App\Controller;

use App\Service\S3Uploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SqsService;

class HomeController extends AbstractController
{
    public function __construct(private SqsService $sqs) {} // <- agregamos esto

    #[Route('/upload', name: 'upload')]
    public function upload(Request $request, S3Uploader $s3Uploader): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class)
            ->getForm();

        $form->handleRequest($request);
        $message = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $key = $s3Uploader->uploadFile($file);
            $message = "Archivo subido como: $key";

            // 📬 Enviar mensaje a SQS
            $this->sqs->sendMessage(json_encode([
                'action' => 'process_upload',
                'filename' => $file->getClientOriginalName(),
                's3_key' => $key,
                'timestamp' => date('c')
            ]));

            $this->addFlash('success', 'Archivo subido y mensaje enviado a la cola.');
        }

        return $this->render('home/upload.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }
}
