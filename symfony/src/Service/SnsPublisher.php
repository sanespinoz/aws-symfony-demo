<?php

namespace App\Service;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;

class SnsPublisher
{
    private SnsClient $snsClient;
    private string $topicArn;

    public function __construct(string $endpoint, string $region, string $key, string $secret, string $topicArn)
    {
        $this->snsClient = new SnsClient([
            'version'     => 'latest',
            'region'      => $region,
            'endpoint'    => $endpoint,
            'credentials' => [
                'key'    => $key,
                'secret' => $secret,
            ],
        ]);

        $this->topicArn = $topicArn;
    }

    public function publish(string $message, array $attributes = []): bool
    {
        try {
            $params = [
                'TopicArn' => $this->topicArn,
                'Message'  => $message,
            ];

            if (!empty($attributes)) {
                $params['MessageAttributes'] = [];
                foreach ($attributes as $key => $value) {
                    $params['MessageAttributes'][$key] = [
                        'DataType' => 'String',
                        'StringValue' => $value,
                    ];
                }
            }

            $result = $this->snsClient->publish($params);
            return isset($result['MessageId']);
        } catch (AwsException $e) {
            // Podés loguear el error aquí o lanzar una excepción
            return false;
        }
    }
}
