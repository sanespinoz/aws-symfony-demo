<?php


namespace App\Service;

use Aws\Sns\SnsClient;

class SnsService
{
    private SnsClient $client;
    private string $topicArn;

    public function __construct(
        string $endpoint,
        string $region,
        string $key,
        string $secret,
        string $topicName
    ) {
        $this->client = new SnsClient([
            'version' => 'latest',
            'region' => $region,
            'endpoint' => $endpoint,
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);

        // Crear topic (idempotente)
        $result = $this->client->createTopic([
            'Name' => $topicName,
        ]);

        $this->topicArn = $result['TopicArn'];
    }

    public function publish(string $message): void
    {
        $this->client->publish([
            'TopicArn' => $this->topicArn,
            'Message' => $message,
        ]);
    }
}
