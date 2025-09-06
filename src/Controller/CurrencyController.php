<?php

namespace App\Controller;

use App\Service\CurrencyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/currency/v1', name: 'currency_')]
final class CurrencyController extends AbstractController
{
    #[Route('/{fromCurrencyCode}/{toCurrencyCode}', name: 'rate', methods: ['GET'])]
    public function index(HttpClientInterface $client, string $fromCurrencyCode, string $toCurrencyCode): JsonResponse
    {
        $currency = (new CurrencyService(client: $client))
            ->setCurrencyCodes($fromCurrencyCode, $toCurrencyCode);
        $currency->fetchRate();
        return $this->json([
            'fallback' => $currency->isFallback(),
            'message' => $currency->fetchRate()
        ]);
    }
}
