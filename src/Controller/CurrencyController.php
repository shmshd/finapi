<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/v1', name: 'currency_')]
final class CurrencyController extends AbstractController
{
    #[Route('/{code}', name: 'rate', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Hi'
        ]);
    }
}
