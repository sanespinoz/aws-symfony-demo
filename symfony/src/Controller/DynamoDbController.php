<?php

namespace App\Controller;

use App\Service\DynamoDbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/users')]
class DynamoDbController extends AbstractController
{
    /*
    #[Route('/dynamo/insert', name: 'dynamo_insert')]
    public function insert(Request $request, DynamoDbService $db): Response
    {
        $userId = $request->query->get('userId', 'test123');
        $name = $request->query->get('name', 'Ejemplo');

        $db->putItem($userId, $name);

        return new Response("Insertado: $userId - $name");
    }

    #[Route('/dynamo/get', name: 'dynamo_get')]
    public function get(Request $request, DynamoDbService $db): Response
    {
        $userId = $request->query->get('userId');

        if (!$userId) {
            return new Response("Falta el parámetro userId", 400);
        }

        $item = $db->getItem($userId);

        if (!$item) {
            return new Response("No se encontró el usuario $userId");
        }

        return $this->render('dynamo/show.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/dynamodb/users', name: 'dynamodb_list')]
    public function list(DynamoDbService $db): Response
    {
        $users = $db->scanItems();

        return $this->render('dynamo/list.html.twig', [
            'users' => $users,
        ]);
    }
        */

    //Endpoints para API RESTful
    public function __construct(private DynamoDbService $db) {}

    #[Route('', name: 'list_users', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json($this->db->scanItems());
    }

    #[Route('/{id}', name: 'get_user', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $item = $this->db->getItem($id);
        return $item ? $this->json($item) : $this->json(['error' => 'Not found'], 404);
    }

    #[Route('', name: 'create_user', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['id'], $data['name'])) {
            return $this->json(['error' => 'Missing id or name'], 400);
        }

        $success = $this->db->putItem($data['id'], $data['name']);
        return $this->json(['success' => $success]);
    }

    #[Route('/{id}', name: 'update_user', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['name'])) {
            return $this->json(['error' => 'Missing name'], 400);
        }

        $success = $this->db->updateItem($id, $data['name']);
        return $this->json(['success' => $success]);
    }

    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $success = $this->db->deleteItem($id);
        return $this->json(['success' => $success]);
    }
}
