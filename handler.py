def handler(event, context):
    print(">>> EVENTO RECIBIDO:", event)
    return {
        'statusCode': 200,
        'body':  "Hello from Lambda!"
    }
