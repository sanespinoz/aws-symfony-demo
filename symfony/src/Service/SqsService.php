<?php

namespace App\Service;

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

class SqsService
{
    private SqsClient $client;
    private string $queueUrl;

    public function __construct(string $endpoint, string $region, string $key, string $secret, string $queueName)
    {
        $this->client = new SqsClient([
            'version' => 'latest',
            'region' => $region,
            'endpoint' => $endpoint,
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);

        // Obtener la URL de la cola
        $result = $this->client->getQueueUrl([
            'QueueName' => $queueName,
        ]);

        $this->queueUrl = $result['QueueUrl'];
    }

    public function sendMessage(string $message): bool
    {
        //$variable = 'Enviando mensaje a SQS:';
        //dd($variable);
        try {
            $this->client->sendMessage([
                'QueueUrl' => $this->queueUrl,
                'MessageBody' => $message,
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }

    public function receiveMessages(int $max = 1): array
    {
        try {
            $result = $this->client->receiveMessage([
                'QueueUrl' => $this->queueUrl,
                'MaxNumberOfMessages' => $max,
            ]);
            return $result->get('Messages') ?? [];
        } catch (AwsException $e) {
            return [];
        }
    }

    public function deleteMessage(string $receiptHandle): void
    {
        $this->client->deleteMessage([
            'QueueUrl' => $this->queueUrl,
            'ReceiptHandle' => $receiptHandle,
        ]);
    }
}
