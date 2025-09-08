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

    #[Route('/{toCurrencyCode}', name: 'rate_', methods: ['GET'])]
    public function withoutToCurrency(
        HttpClientInterface $client,
        string $toCurrencyCode
    ): JsonResponse
    {
        $baseCurrency = CurrencyService::DEFAULT_BASE_CURRENCY;
        $currency = new CurrencyService(client: $client)
            ->setCurrencyCodes(CurrencyService::DEFAULT_BASE_CURRENCY, $toCurrencyCode);
        $rate = $currency->fetchRate();
        return $this->json([
            'fallback' => $currency->isFallback(),
            'currency' => ['base' => $baseCurrency, 'to' => $toCurrencyCode],
            'rate' => $rate
        ]);
    }

    #[Route('/{fromCurrencyCode}/{toCurrencyCode}', name: 'rate', methods: ['GET'])]
    public function withToCurrency(
        HttpClientInterface $client,
        string $fromCurrencyCode,
        string $toCurrencyCode
    ): JsonResponse
    {
        $currency = new CurrencyService(client: $client)
            ->setCurrencyCodes($fromCurrencyCode, $toCurrencyCode);
        $rate = $currency->fetchRate();
        return $this->json([
            'fallback' => $currency->isFallback(),
            'currency' => ['base' => $fromCurrencyCode, 'to' => $toCurrencyCode],
            'rate' => $rate
        ]);
    }
}
