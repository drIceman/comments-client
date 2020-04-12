<?php

declare(strict_types=1);

namespace Example\CommentsClient;

use Example\CommentsClient\Exceptions\BadRequestException;
use Example\CommentsClient\Responses\CommentsResponse;
use Example\CommentsClient\Responses\DevPingResponse;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\ClientInterface as GuzzleInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions as GuzzleOptions;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface, WithSettingsInterface
{
    protected Settings $settings;
    protected GuzzleInterface $guzzle;

    /**
     * Создание нового экземпляра.
     *
     * @param Settings $settings
     * @param GuzzleInterface|null $guzzle
     */
    public function __construct(Settings $settings, ?GuzzleInterface $guzzle = null)
    {
        $this->settings = $settings;
        $this->guzzle = $guzzle ?? new Guzzle();
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings(): Settings
    {
        return $this->settings;
    }

    /**
     * {@inheritdoc}
     */
    public function devPing(?string $value = null): DevPingResponse
    {
        return DevPingResponse::fromHttpResponse(
            $this->doRequest(
                new Request('get', 'dev/ping'),
                [
                    'query' => [
                        'value' => $value ?? ((string)time()),
                    ],
                ]
            )
        );
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     *
     * @return ResponseInterface
     *
     * @throws BadRequestException
     */
    protected function doRequest(RequestInterface $request, array $options = []): ResponseInterface
    {
        $options = array_replace($this->settings->getGuzzleOptions(), $options);
        $options['base_uri'] = $this->settings->getBaseUri();

        try {
            $response = $this->guzzle->send($request, $options);
        } catch (GuzzleException $e) {
            throw new BadRequestException($e->getRequest(), $e->getResponse(), null, null, $e);
        }

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function getComments(): CommentsResponse
    {
        return CommentsResponse::fromHttpResponse(
            $this->doRequest(
                new Request('get', 'comments')
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function createComment(string $name, string $text): CommentsResponse
    {
        if (!$name || !$text) {
            throw new InvalidArgumentException('Missing required parameters.');
        }

        return CommentsResponse::fromHttpResponse(
            $this->doRequest(
                new Request('post', 'comment'),
                [
                    GuzzleOptions::JSON => [
                        'name' => $name,
                        'text' => $text,
                    ],
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function updateComment(int $id, ?string $name, ?string $text): CommentsResponse
    {
        if (!$id || (!$name && !$text)) {
            throw new InvalidArgumentException('Missing required parameters.');
        }

        return CommentsResponse::fromHttpResponse(
            $this->doRequest(
                new Request('put', 'comment/' . $id),
                [
                    GuzzleOptions::JSON => [
                        'id' => $id,
                        'name' => $name,
                        'text' => $text,
                    ],
                ]
            )
        );
    }
}
