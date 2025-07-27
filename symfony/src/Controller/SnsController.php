<?php

namespace App\Controller;

use App\Service\SnsPublisher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SnsController extends AbstractController
{
    private SnsPublisher $snsPublisher;

    public function __construct(SnsPublisher $snsPublisher)
    {
        $this->snsPublisher = $snsPublisher;
    }

    #[Route('/sns/publish', name: 'sns_publish')]
    public function publish(): Response
    {
        $message = 'Hola desde Symfony y SNS!';
        $success = $this->snsPublisher->publish($message);

        if ($success) {
            return new Response('Mensaje publicado en SNS correctamente.');
        } else {
            return new Response('Error publicando el mensaje.', 500);
        }
    }
}
