<?php

namespace App\Service;

use Currency\Util\CurrencySymbolUtil;
use DateTime;
use InvalidArgumentException;
use Symfony\Component\Intl\Currencies;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CurrencyService
{
    private const string ENDPOINT_VERSION = 'v1';
    public const string DEFAULT_BASE_CURRENCY = 'usd';
    private readonly string $fromCurrencyCode;
    private readonly string $toCurrencyCode;
    private readonly string $date;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly bool $is_fallback = false,
    )
    {
        $this->setDate();
    }

//    public function getRate(string $baseCurrencyCode): string
//    {
//    }

//    private function getRateInFormat(): string
//    {
//    }

    public function setDate(string $date = 'latest'): CurrencyService
    {
        if ($this->isDate($date)) {
            $this->date = $date;
        }

        return $this;
    }

    public function setCurrencyCode(string $fromCurrencyCode, ?string $toCurrencyCode = null): CurrencyService
    {
        $this->fromCurrencyCode = strtolower($fromCurrencyCode);
        if ($toCurrencyCode) {
            $this->toCurrencyCode = strtolower($toCurrencyCode);
        }

        return $this;
    }

    public function fetchRate(): float
    {
        $request = $this->client->request('GET', $this->getEndpoint($this->fromCurrencyCode));
        return $request->toArray()[$this->fromCurrencyCode][$this->toCurrencyCode];
    }

    public function fetchRates(): array
    {
        $request = $this->client->request('GET', $this->getEndpoint($this->fromCurrencyCode));
        return $request->toArray()[$this->fromCurrencyCode];
    }

    public function isFallback(): bool
    {
        return $this->is_fallback;
    }

    public function getCurrencySymbol($currencyCode): string
    {
        try {
            return CurrencySymbolUtil::getSymbol($currencyCode);
        } catch (InvalidArgumentException) {
            return Currencies::getSymbol($currencyCode);
        }
    }

    public function getEndpoint($fromCurrencyCode): string
    {
        return sprintf("%s/currencies/%s.min.json",
            $this->getCurrentEndpointBase(), strtolower($fromCurrencyCode));
    }

    public function getCurrentEndpointBase(): string
    {
        if (!$this->isDate($this->date)) {
            throw new \DateException();
        }

        return !$this->is_fallback ?
            $this->getEndpointBases()[0] :
            $this->getEndpointBases()[1];
    }

    public function getEndpointBases(): array
    {
        return [
            sprintf('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@%s/%s',
                $this->date, self::ENDPOINT_VERSION),
            sprintf('https://%s.currency-api.pages.dev/%s',
                $this->date, self::ENDPOINT_VERSION)
        ];
    }

    private function isDate($date): bool
    {
        if ($date !== 'latest') {
            $dt = DateTime::createFromFormat("Y-m-d", $date);
            return $dt !== false && $dt::getLastErrors() === false;
        }
        return true;
    }

}
