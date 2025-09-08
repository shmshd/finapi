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

    #[Route('/rates/{baseCurrency}', name: 'all_rates', methods: ['GET'])]
    public function rates(
        HttpClientInterface $client,
        string $baseCurrency = CurrencyService::DEFAULT_BASE_CURRENCY
    ): JsonResponse
    {
        $currency = new CurrencyService(client: $client)
            ->setCurrencyCode($baseCurrency);
        $rates = $currency->fetchRates();
        return $this->json([
            'fallback' => $currency->isFallback(),
            'base' => $baseCurrency,
            'rates' => $rates
        ]);
    }

    #[Route('/rate/{toCurrencyCode}', name: 'rate_without_to_currency', methods: ['GET'])]
    public function withoutToCurrency(
        HttpClientInterface $client,
        string $toCurrencyCode
    ): JsonResponse
    {
        $baseCurrency = CurrencyService::DEFAULT_BASE_CURRENCY;
        $currency = new CurrencyService(client: $client)
            ->setCurrencyCode(CurrencyService::DEFAULT_BASE_CURRENCY, $toCurrencyCode);
        $rate = $currency->fetchRate();
        return $this->json([
            'fallback' => $currency->isFallback(),
            'currency' => ['base' => $baseCurrency, 'to' => $toCurrencyCode],
            'rate' => $rate
        ]);
    }

    #[Route('/rate/{fromCurrencyCode}/{toCurrencyCode}', name: 'rate_with_to_currency', methods: ['GET'])]
    public function withToCurrency(
        HttpClientInterface $client,
        string $fromCurrencyCode,
        string $toCurrencyCode
    ): JsonResponse
    {
        $currency = new CurrencyService(client: $client)
            ->setCurrencyCode($fromCurrencyCode, $toCurrencyCode);
        $rate = $currency->fetchRate();
        return $this->json([
            'fallback' => $currency->isFallback(),
            'currency' => ['base' => $fromCurrencyCode, 'to' => $toCurrencyCode],
            'rate' => $rate
        ]);
    }
}
