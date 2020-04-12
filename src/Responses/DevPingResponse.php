<?php

declare(strict_types=1);

namespace Example\CommentsClient\Responses;

use Example\CommentsClient\Exceptions\BadResponseException;
use GuzzleHttp\Exception\InvalidArgumentException;
use http\Exception\BadConversionException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

class DevPingResponse implements ResponseInterface
{
    protected string $rawResponseContent;
    protected string $value;
    protected int $in;
    protected int $out;
    protected int $delay;

    /**
     * Создание нового экземпляра.
     *
     * @param string $rawResponse
     * @param string $value
     * @param int $in
     * @param int $out
     * @param int $delay
     */
    private function __construct(string $rawResponse, string $value, int $in, int $out, int $delay)
    {
        $this->rawResponseContent = $rawResponse;
        $this->value = $value;
        $this->in = $in;
        $this->out = $out;
        $this->delay = $delay;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadConversionException
     */
    public static function fromHttpResponse(HttpResponseInterface $response): self
    {
        try {
            $items = \GuzzleHttp\json_decode($rawResponse = (string)$response->getBody(), true);
        } catch (InvalidArgumentException $e) {
            throw new BadResponseException($response, 'Response conversion error.', 0, $e);
        }

        return new static(
            $rawResponse,
            $items['value'],
            $items['in'],
            $items['out'],
            $items['delay']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRawResponseContent(): string
    {
        return $this->rawResponseContent;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * @return int
     */
    public function getIn(): int
    {
        return $this->in;
    }

    /**
     * @return int
     */
    public function getOut(): int
    {
        return $this->out;
    }
}
