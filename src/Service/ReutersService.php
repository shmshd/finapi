<?php

namespace App\Service;

use Symfony\Component\Panther\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ReutersService
{
    public const string HOST = 'https://www.reuters.com';

    public const array ROUTE_PATTERNS = [
        'stock' => '/markets/companies/',
        'index' => '/markets/quote/.',
    ];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $exchangeCode,
        private readonly string $type
    )
    {}

    public function getPageSource(): float
    {
        $client = Client::createChromeClient();
        $client->request('GET', $this->getEndpoint());

//        $request = $this->client->request('GET', $this->getEndpoint(), [
//            'headers' => [
//                'Cookie' => 'reuters-geo={"country":"AE", "region":"-"};OptanonConsent=isGpcEnabled=1&datestamp=Tue+Sep+09+2025+10%3A47%3A20+GMT%2B0400+(Gulf+Standard+Time)&version=202505.2.0&hosts=&groups=1%3A1%2C2%3A0%2C3%3A1%2C4%3A0;',
//                'UserAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36',
//            ]
//        ]);
        return $client->getPageSource();
    }

    private function getEndpoint(): string
    {
        if (!array_key_exists($this->type, self::ROUTE_PATTERNS)) {
            throw new \Exception('Invalid type: ' . $this->type);
        }
        return self::HOST.self::ROUTE_PATTERNS[$this->type].strtoupper($this->exchangeCode);
    }
}
