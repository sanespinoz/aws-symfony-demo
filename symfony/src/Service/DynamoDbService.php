<?php

namespace App\Service;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;

class DynamoDbService
{
    private $client;
    private $tableName;

    public function __construct(string $endpoint, string $region, string $key, string $secret, string $tableName)
    {
        $this->tableName = $tableName;

        $this->client = new DynamoDbClient([
            'version' => 'latest',
            'region'  => $region,
            'endpoint' => $endpoint,
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);
    }

    public function putItem(string $userId, string $name): bool
    {
        try {
            $this->client->putItem([
                'TableName' => $this->tableName,
                'Item' => [
                    'UserId' => ['S' => $userId],
                    'Name' => ['S' => $name],
                ],
            ]);
            return true;
        } catch (DynamoDbException $e) {
            // manejar error
            return false;
        }
    }

    public function getItem(string $userId): ?array
    {
        try {
            $result = $this->client->getItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'UserId' => ['S' => $userId],
                ],
            ]);
            return $result['Item'] ?? null;
        } catch (DynamoDbException $e) {
            return null;
        }
    }

    public function scanItems(): array
    {
        try {
            $result = $this->client->scan([
                'TableName' => $this->tableName,
            ]);
            return $result['Items'] ?? [];
        } catch (DynamoDbException $e) {
            return [];
        }
    }


    public function updateItem(string $userId, string $newName): bool
    {
        try {
            $this->client->updateItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'UserId' => ['S' => $userId],
                ],
                'UpdateExpression' => 'SET #name = :newName',
                'ExpressionAttributeNames' => [
                    '#name' => 'Name',
                ],
                'ExpressionAttributeValues' => [
                    ':newName' => ['S' => $newName],
                ],
            ]);
            return true;
        } catch (DynamoDbException $e) {
            return false;
        }
    }

    public function deleteItem(string $userId): bool
    {
        try {
            $this->client->deleteItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'UserId' => ['S' => $userId],
                ],
            ]);
            return true;
        } catch (DynamoDbException $e) {
            return false;
        }
    }

    public function tableExists(string $tableName): bool
    {
        try {
            $this->client->describeTable(['TableName' => $tableName]);
            return true;
        } catch (DynamoDbException $e) {
            return $e->getAwsErrorCode() !== 'ResourceNotFoundException' ? throw $e : false;
        }
    }

    public function listTables(): array
    {
        $result = $this->client->listTables();
        return $result['TableNames'] ?? [];
    }

    #crear una tabla DynamoDB desde Symfony
    public function createTable(string $tableName): bool
    {
        try {
            $this->client->createTable([
                'TableName' => $tableName,
                'AttributeDefinitions' => [
                    [
                        'AttributeName' => 'UserId',
                        'AttributeType' => 'S',
                    ],
                ],
                'KeySchema' => [
                    [
                        'AttributeName' => 'UserId',
                        'KeyType' => 'HASH',
                    ],
                ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits' => 5,
                    'WriteCapacityUnits' => 5,
                ],
            ]);

            // Esperar a que esté activa
            $this->client->waitUntil('TableExists', [
                'TableName' => $tableName,
            ]);

            return true;
        } catch (DynamoDbException $e) {
            return false;
        }
    }
}
