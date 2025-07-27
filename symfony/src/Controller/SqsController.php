<?php

namespace App\Controller;

use App\Service\SqsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/queue')]
class SqsController extends AbstractController
{
    public function __construct(private SqsService $sqs) {}

    #[Route('/send', name: 'queue_send_api', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['message'])) {
            return $this->json(['error' => 'Missing message'], 400);
        }

        $success = $this->sqs->sendMessage($data['message']);

        return $this->json(['success' => $success]);
    }

    #[Route('/receive', name: 'queue_receive', methods: ['GET'])]
    public function receive(): JsonResponse
    {
        $messages = $this->sqs->receiveMessages(5); // hasta 5 mensajes

        return $this->json(['messages' => $messages]);
    }

    #[Route('/delete', name: 'queue_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $receiptHandle = $request->request->get('receipt_handle');

        if (!$receiptHandle) {
            return $this->redirectToRoute('queue_index');
        }

        $this->sqs->deleteMessage($receiptHandle);

        return $this->redirectToRoute('queue_index');
    }
}
