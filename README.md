# 🧪 Proyecto Symfony con AWS SNS, SQS, S3, Lambda y DynamoDB usando LocalStack

Este proyecto Symfony demuestra cómo integrar múltiples servicios de AWS simulados localmente mediante LocalStack. Está diseñado para mostrar una integración avanzada entre Symfony y servicios como **SNS**, **SQS**, **S3**, **Lambda** y **DynamoDB**, todo dentro de un entorno Dockerizado.

---

## 🚀 Tecnologías utilizadas

- Symfony 5.4 (PHP 8.2)
- Docker + Docker Compose
- LocalStack para simular servicios AWS (SNS, SQS, S3, Lambda, DynamoDB)
- AWS SDK para PHP
- Symfony Messenger (para consumo de mensajes desde SQS)
- Nginx como servidor web

---

## 📁 Estructura del proyecto

```text
.
├── docker/                 # Configuración Docker y PHP
├── localstack/             # Scripts para inicializar servicios AWS
├── nginx/                  # Configuración de Nginx
├── symfony/                # Código fuente Symfony
├── docker-compose.yml      # Orquestación de contenedores
└── README.md               # Documentación
```

---

## 🧪 Cómo ejecutar el proyecto

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/aws-symfony-demo.git
cd aws-symfony-demo
```

### 2. Levantar los contenedores Docker (esto construye imágenes y levanta el stack completo):

```bash
docker compose up -d --build
```

### 3. Acceder a Symfony desde el navegador

Visitar: [http://localhost:8080](http://localhost:8080)

### 4. Subir un archivo (ejemplo endpoint)

Visitar: [http://localhost:8080/upload]

### 5. Probar operaciones con DynamoDB

- Listar usuarios (GET):

  [http://localhost:8080/dynamo/users](http://localhost:8080/dynamo/users)

- Crear usuario (POST):

  [http://localhost:8080/dynamo/create-user](http://localhost:8080/dynamo/create-user)

Podés usar Postman o curl para hacer la prueba.


---

## 📬 Probar publicación SNS

Ingresar en el navegador o usar `curl`:

```bash
curl http://localhost:8080/sns/publish
```

Esto envía un mensaje a un topic SNS que a su vez se suscribe a una cola SQS.

---

## 📥 Consumir mensajes de SQS con Symfony Messenger

```bash
docker compose exec php php bin/console messenger:consume sns_sqs -vv
```

---

## 🧪 Servicios inicializados con LocalStack

Los scripts en la carpeta `localstack/` se ejecutan automáticamente al iniciar el contenedor de LocalStack y configuran:

- Bucket en **S3**
- Tabla en **DynamoDB**
- Función **Lambda**
- Topic **SNS**, cola **SQS** y suscripción **SNS → SQS**

---

## 🔐 Variables de entorno necesarias

Agregá estas variables en tu archivo `.env` o `.env.local`:

```dotenv
AWS_SQS_KEY=test
AWS_SQS_SECRET=test
AWS_SQS_REGION=us-east-1
AWS_SQS_ENDPOINT=http://localstack:4566

AWS_SNS_KEY=test
AWS_SNS_SECRET=test
AWS_SNS_REGION=us-east-1
AWS_SNS_ENDPOINT=http://localstack:4566
AWS_SNS_TOPIC_NAME=my-topic
```

---

## 💡 ¿Por qué este proyecto?

Este ejemplo muestra cómo construir una arquitectura desacoplada y escalable usando Symfony + AWS (simulado localmente). Es ideal para:

- ✅ Demostrar experiencia con AWS, Docker, y PHP/Symfony  
- 🧪 Preparar entornos locales para pruebas realistas  
- 🔄 Aplicar patrones de mensajería asincrónica con Symfony Messenger  
- 📬 Usar SQS como cola de eventos y SNS como sistema de publicación/suscripción
