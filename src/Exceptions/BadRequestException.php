<?php

declare(strict_types=1);

namespace Example\CommentsClient\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class BadRequestException extends RuntimeException
{
    protected RequestInterface $httpRequest;
    protected ?ResponseInterface $httpResponse;

    /**
     * Create a new exception instance.
     *
     * @param RequestInterface $httpRequest
     * @param ResponseInterface|null $httpResponse
     * @param string|null $message
     * @param int|null $code
     * @param Throwable|null $previous
     */
    public function __construct(
        RequestInterface $httpRequest,
        ?ResponseInterface $httpResponse = null,
        ?string $message = null,
        ?int $code = null,
        ?Throwable $previous = null
    ) {
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
        $serviceErrorMessage = null;
        $httpCode = null;

        if ($httpResponse instanceof ResponseInterface) {
            $httpCode = $httpResponse->getStatusCode();
        }

        $previousExceptionMessage = $previous instanceof Throwable && $previous->getMessage() !== ''
            ? $previous->getMessage()
            : null;

        parent::__construct(
            $message ?? $previousExceptionMessage ?? 'Unsuccessful request',
            $code ?? $httpCode ?? 0,
            $previous
        );
    }

    /**
     * @return RequestInterface
     */
    public function getHttpRequest(): RequestInterface
    {
        return $this->httpRequest;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getHttpResponse(): ?ResponseInterface
    {
        return $this->httpResponse;
    }
}
