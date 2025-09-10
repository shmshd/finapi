<?php

namespace App\Controller;

use App\Service\ReutersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/market/v1', name: 'market_')]
final class MarketController extends AbstractController
{

    #[Route('/{exchangeCode}', name: 'company', methods: ['GET'])]
    public function market(
        HttpClientInterface $client,
        string              $exchangeCode
    ): JsonResponse
    {
        $market = new ReutersService($client, $exchangeCode, 'stock');
//        $market->getPageSource();
        dd($market->getPageSource());
        return $this->json([]);
    }
}
