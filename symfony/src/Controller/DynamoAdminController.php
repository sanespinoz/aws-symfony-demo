<?php

namespace App\Controller;

use App\Service\DynamoDbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dynamodb')]
class DynamoAdminController extends AbstractController
{
    public function __construct(private DynamoDbService $db) {}

    #[Route('/tables', name: 'dynamodb_list_tables', methods: ['GET'])]
    public function listTables(): JsonResponse
    {
        $tables = $this->db->listTables();
        return $this->json(['tables' => $tables]);
    }

    #[Route('/table-exists/{name}', name: 'dynamodb_table_exists', methods: ['GET'])]
    public function tableExists(string $name): JsonResponse
    {
        $exists = $this->db->tableExists($name);
        return $this->json([
            'table' => $name,
            'exists' => $exists,
        ]);
    }

    #[Route('/create-table/{name}', name: 'dynamodb_create_table', methods: ['POST'])]
    public function createTable(string $name): JsonResponse
    {
        if ($this->db->tableExists($name)) {
            return $this->json(['message' => "La tabla '$name' ya existe."], 400);
        }

        $created = $this->db->createTable($name);
        return $created
            ? $this->json(['message' => "Tabla '$name' creada correctamente."])
            : $this->json(['error' => "No se pudo crear la tabla '$name'"], 500);
    }
}
