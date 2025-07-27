<?php

namespace App\MessageHandler;

use App\Message\SnsMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SnsMessageHandler
{
    public function __invoke(SnsMessage $message)
    {
        echo "✅ Mensaje recibido: " . $message->content . PHP_EOL;
        // acá podrías hacer lógica de negocio
    }
}
