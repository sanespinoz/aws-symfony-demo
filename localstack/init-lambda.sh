#!/bin/bash
set -e

# Crear la cola si no existe (idempotente)
awslocal sqs create-queue --queue-name my-queue

# Crear la función lambda
awslocal lambda create-function --function-name my-function \
  --runtime python3.9 \
  --handler handler.handler \
  --role arn:aws:iam::000000000000:role/lambda-role \
  --zip-file fileb:///tmp/function.zip

# Esperar la cola (get-queue-url en loop)
QUEUE_URL=""
while [ -z "$QUEUE_URL" ]; do
  echo "Esperando a que la cola SQS esté disponible..."
  QUEUE_URL=$(awslocal sqs get-queue-url --queue-name my-queue --output text 2>/dev/null || true)
  sleep 2
done

QUEUE_ARN=$(awslocal sqs get-queue-attributes --queue-url "$QUEUE_URL" --attribute-names QueueArn --output text | cut -f2)

echo "Cola SQS lista con ARN: $QUEUE_ARN"

# Crear el event source mapping
awslocal lambda create-event-source-mapping \
  --function-name my-function \
  --event-source-arn "$QUEUE_ARN" \
  --batch-size 1
