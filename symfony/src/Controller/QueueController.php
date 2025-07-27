<?php

namespace App\Controller;

use App\Service\SqsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/queue')]
class QueueController extends AbstractController
{
    public function __construct(private SqsService $sqs) {}

    // Lista los mensajes de la cola
    #[Route('', name: 'queue_index')]
    public function index(): Response
    {
        $messages = $this->sqs->receiveMessages(10); // recibe hasta 10 mensajes
        return $this->render('queue/index.html.twig', [
            'messages' => $messages
        ]);
    }


    #[Route('/send', name: 'queue_send', methods: ['POST'])]
    public function send(Request $request): Response
    {
        $message = $request->request->get('message');
        if ($message) {
            $this->sqs->sendMessage($message);
            $this->addFlash('success', 'Mensaje enviado.');
        } else {
            $this->addFlash('danger', 'Debe ingresar un mensaje.');
        }
        return $this->redirectToRoute('queue_index');
    }



    // Elimina un mensaje de la cola
    #[Route('/delete/{receiptHandle}', name: 'queue_delete')]
    public function delete(string $receiptHandle): Response
    {
        $this->sqs->deleteMessage($receiptHandle);
        $this->addFlash('success', 'Mensaje eliminado.');
        return $this->redirectToRoute('queue_index');
    }
}
