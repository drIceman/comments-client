<?php

declare(strict_types=1);

namespace Example\CommentsClient;

use GuzzleHttp\RequestOptions as GuzzleHttpOptions;

class Settings
{
    protected string $baseUri = 'https://dummy.server/api/v1/';

    protected array $guzzleOptions = [
        GuzzleHttpOptions::VERIFY => true,
        GuzzleHttpOptions::TIMEOUT => 60.0,
    ];

    /**
     * Создание нового экземпляра.
     *
     * @param null|string $baseUri
     * @param null|array $guzzleOptions
     */
    public function __construct(?string $baseUri = null, ?array $guzzleOptions = null)
    {
        if (is_string($baseUri)) {
            $this->baseUri = rtrim($baseUri, '/ ') . '/';
        }

        if (is_array($guzzleOptions)) {
            $this->guzzleOptions = array_replace($this->guzzleOptions, $guzzleOptions);
        }
    }

    /**
     * Получить адрес API.
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * Получить опции Guzzle.
     *
     * @return array
     */
    public function getGuzzleOptions(): array
    {
        return $this->guzzleOptions;
    }
}
