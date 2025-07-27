#!/bin/bash
set -e

echo "Inicializando SNS y nueva cola..."

# Crear el topic SNS
awslocal sns create-topic --name my-topic

# Crear la nueva cola SQS para recibir mensajes del topic
awslocal sqs create-queue --queue-name sns-queue

# Esperar que la cola exista
QUEUE_URL=""
while [ -z "$QUEUE_URL" ]; do
  echo "Esperando la cola SQS..."
  QUEUE_URL=$(awslocal sqs get-queue-url --queue-name sns-queue --output text 2>/dev/null || true)
  sleep 1
done

# Obtener ARN de la cola
QUEUE_ARN=$(awslocal sqs get-queue-attributes --queue-url "$QUEUE_URL" --attribute-names QueueArn --query 'Attributes.QueueArn' --output text)

# Obtener ARN del topic SNS
TOPIC_ARN=$(awslocal sns list-topics --query 'Topics[0].TopicArn' --output text)

# Suscribir la cola al topic SNS
awslocal sns subscribe \
  --topic-arn "$TOPIC_ARN" \
  --protocol sqs \
  --notification-endpoint "$QUEUE_ARN"

# Crear la política JSON escapada
POLICY_ESCAPED=$(printf '{"Version":"2012-10-17","Statement":[{"Sid":"AllowSNSPublish","Effect":"Allow","Principal":"*","Action":"sqs:SendMessage","Resource":"%s","Condition":{"ArnEquals":{"aws:SourceArn":"%s"}}}]}' "$QUEUE_ARN" "$TOPIC_ARN" | sed 's/"/\\"/g')

# Asignar la política a la cola
awslocal sqs set-queue-attributes \
  --queue-url "$QUEUE_URL" \
  --attributes "{\"Policy\": \"$POLICY_ESCAPED\"}"

echo "SNS y suscripción configurados correctamente ✅"
