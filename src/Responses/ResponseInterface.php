<?php

declare(strict_types=1);

namespace Example\CommentsClient\Responses;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface ResponseInterface
{
    /**
     * Создание экземпляра из HTTP response.
     *
     * @param HttpResponseInterface $response
     *
     * @return self
     */
    public static function fromHttpResponse(HttpResponseInterface $response): self;
}
