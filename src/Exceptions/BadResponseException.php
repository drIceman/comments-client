<?php

declare(strict_types=1);

namespace Example\CommentsClient\Exceptions;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class BadResponseException extends RuntimeException
{
    protected ResponseInterface $httpResponse;

    /**
     * Создание нового экземпляра.
     *
     * @param ResponseInterface $httpResponse
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        ResponseInterface $httpResponse,
        string $message,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->httpResponse = $httpResponse;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ResponseInterface
     */
    public function getHttpResponse(): ResponseInterface
    {
        return $this->httpResponse;
    }
}
