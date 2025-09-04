<?php

namespace App\Service;

use Currency\Util\CurrencySymbolUtil;
use DateTime;
use InvalidArgumentException;
use Symfony\Component\Intl\Currencies;

final class CurrencyService
{
    private const ENDPOINT_VERSION = 'v1';

    public function __construct(
        private readonly string $date = "latest",
        private readonly bool $is_fallback = false
    )
    {
    }

//    public function getRate(string $baseCurrencyCode): string
//    {
//    }

//    private function getRateInFormat(): string
//    {
//    }

    private function getCurrencySymbol($currencyCode): string
    {
        try {
            return CurrencySymbolUtil::getSymbol($currencyCode);
        } catch (InvalidArgumentException) {
            return Currencies::getSymbol($currencyCode);
        }
    }

    public function getCurrentEndpoint(): string
    {
        $ev = self::ENDPOINT_VERSION;
        if (!$this->isDate($this->date)) {
            throw new \DateException();
        }

        return !$this->is_fallback ?
            $this->getEndpoints()[0] :
            $this->getEndpoints()[1];
    }

    public function getEndpoints(): array
    {
        return [
            sprintf('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@%s/%s', $this->date, self::ENDPOINT_VERSION),
            sprintf('https://%s.currency-api.pages.dev/%s', $this->date, self::ENDPOINT_VERSION)
        ];
    }

    private function isDate($date): bool
    {
        $dt = DateTime::createFromFormat("Y-m-d", $date);
        return $dt !== false && $dt::getLastErrors() === false;
    }

}
