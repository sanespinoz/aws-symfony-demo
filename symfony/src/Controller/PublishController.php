<?php

namespace App\Controller;

use App\Service\SnsPublisher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    #[Route('/publish', name: 'publish_message')]
    public function publish(SnsPublisher $publisher): JsonResponse
    {
        $publisher->publish('Hola desde SNS y Symfony 🎯');

        return new JsonResponse(['status' => 'mensaje publicado']);
    }
}
