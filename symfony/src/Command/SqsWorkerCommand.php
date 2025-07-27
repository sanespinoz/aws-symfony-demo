<?php

namespace App\Command;

use App\Service\SqsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sqs-worker')]
class SqsWorkerCommand extends Command
{
    public function __construct(private SqsService $sqs)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Escucha mensajes de SQS y los procesa uno por uno.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Esperando mensajes de SQS...</info>");

        while (true) {
            $messages = $this->sqs->receiveMessages(5);

            if (empty($messages)) {
                $output->writeln("[" . date('H:i:s') . "] Sin mensajes, esperando...");
                sleep(3);
                continue;
            }

            foreach ($messages as $message) {
                $body = json_decode($message['Body'], true);

                if (!$body || !isset($body['action'])) {
                    $output->writeln("<comment>Mensaje inválido recibido:</comment> " . $message['Body']);
                    continue;
                }

                if ($body['action'] === 'process_upload') {
                    $filename = $body['filename'] ?? 'desconocido';
                    $s3Key = $body['s3_key'] ?? '(sin key)';

                    $output->writeln("<info>📂 Procesando archivo:</info> $filename (S3 Key: $s3Key)");
                    // Acá podrías simular procesamiento, generar thumbnails, etc.
                }

                $this->sqs->deleteMessage($message['ReceiptHandle']);
            }

            sleep(1); // pequeña pausa para evitar saturación
        }

        return Command::SUCCESS;
    }
}
