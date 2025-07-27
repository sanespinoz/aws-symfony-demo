<?php

namespace App\Controller;

use App\Service\SnsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SnsTestController extends AbstractController
{
    #[Route('/test/sns', name: 'test_sns')]
    public function test(SnsService $snsService): Response
    {
        $snsService->publish('Mensaje SNS desde Symfony');

        return new Response('Mensaje enviado a SNS');
    }
}
