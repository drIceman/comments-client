<?php

declare(strict_types=1);

namespace Example\CommentsClient\Responses;

use ArrayIterator;
use Example\CommentsClient\Exceptions\BadResponseException;
use Example\CommentsClient\Types\CommentType;
use Example\CommentsClient\Types\CommentTypeInterface;
use GuzzleHttp\Exception\InvalidArgumentException;
use IteratorAggregate;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

class CommentsResponse implements ResponseInterface, IteratorAggregate
{
    protected string $rawResponseContent;

    /**
     * @var CommentTypeInterface[]
     */
    protected array $data;

    /**
     * Создание нового экземпляра.
     *
     * @param string $rawResponse
     * @param CommentTypeInterface[] $data
     */
    public function __construct(string $rawResponse, array $data)
    {
        $this->rawResponseContent = $rawResponse;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadResponseException
     */
    public static function fromHttpResponse(HttpResponseInterface $response): self
    {
        try {
            $items = \GuzzleHttp\json_decode($rawResponse = (string)$response->getBody(), true);
        } catch (InvalidArgumentException $e) {
            throw new BadResponseException($response, 'Response conversion error.', 0, $e);
        }

        $items['data'] = array_map(
            static function (array $reportData): CommentTypeInterface {
                return CommentType::fromArray($reportData);
            },
            $items['data']
        );

        return new static($rawResponse, $items['data']);
    }

    /**
     * {@inheritdoc}
     */
    public function getRawResponseContent(): string
    {
        return $this->rawResponseContent;
    }

    /**
     * Получить данные.
     *
     * @return CommentTypeInterface[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }
}
