<?php

namespace App\Infrastructure\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Demo extends AbstractController
{
    #[Route('/demo', name: 'app_infrastructure_http_controller_demo')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Infrastructure/Http/Controller/Demo.php',
        ]);
    }
}
