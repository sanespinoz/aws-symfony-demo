<?php

namespace App\Controller;

use App\Service\DynamoDbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DynamoDbTestController extends AbstractController
{
    #[Route('/dynamodb-test', name: 'dynamodb_test')]
    public function test(DynamoDbService $dynamoDb): Response
    {
        $dynamoDb->putItem('user123', 'Liliana');

        $user = $dynamoDb->getItem('user123');

        return new Response('<pre>' . print_r($user, true) . '</pre>');
    }
}
