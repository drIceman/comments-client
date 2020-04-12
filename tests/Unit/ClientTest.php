<?php

declare(strict_types=1);

namespace Example\CommentsClient\Test\Unit;

use Example\CommentsClient\Client;
use Example\CommentsClient\ClientInterface;
use Example\CommentsClient\Exceptions\BadRequestException;
use Example\CommentsClient\Exceptions\BadResponseException;
use Example\CommentsClient\Settings;
use Example\CommentsClient\Test\AbstractTestCase;
use Example\CommentsClient\Types\CommentType;
use Example\CommentsClient\WithSettingsInterface;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;

/**
 * @covers \Example\CommentsClient\Client
 */
class ClientTest extends AbstractTestCase
{
    protected Guzzle $guzzle;
    protected Client $client;
    protected Settings $settings;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Client(
            $this->settings = new Settings(),
            $this->guzzle = new Guzzle(
                [
                    'handler' => HandlerStack::create($this->guzzleHandler),
                ]
            )
        );
    }

    /**
     * @return void
     * @uses \Example\CommentsClient\Client
     *
     * @uses \Example\CommentsClient\Settings
     */
    public function testImplementations(): void
    {
        $this->assertInstanceOf(ClientInterface::class, $this->client);
        $this->assertInstanceOf(WithSettingsInterface::class, $this->client);
    }

    /**
     * @return void
     * @uses \Example\CommentsClient\Responses\DevPingResponse
     * @uses \Example\CommentsClient\Exceptions\BadResponseException
     *
     * @uses \Example\CommentsClient\Settings
     */
    public function testDoRequestWithWrongJsonResponse(): void
    {
        $this->expectException(BadResponseException::class);
        $this->expectExceptionMessageMatches('~conversion error~i');

        $this->guzzleHandler->onUriRegexpRequested(
            '~' . preg_quote($this->settings->getBaseUri(), '/') . '.*~i',
            'get',
            new Response(
                200, ['content-type' => 'application/json;charset=utf-8'], '{"foo":]'
            ),
            true
        );

        $this->client->devPing();
    }

    /**
     * @return void
     * @uses \Example\CommentsClient\Exceptions\BadRequestException
     *
     * @uses \Example\CommentsClient\Settings
     */
    public function testDoRequestWithServerError(): void
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessageMatches('~Failed to connect~i');

        $this->guzzleHandler->onUriRegexpRequested(
            '~' . preg_quote($this->settings->getBaseUri(), '/') . '.*~i',
            $method = 'get',
            new ConnectException(
                'cURL error 7: Failed to connect to host: Connection refused...',
                new Request($method, $this->settings->getBaseUri() . 'dev/ping')
            ),
            true
        );

        $this->client->devPing();
    }

    /**
     * @return void
     * @uses \Example\CommentsClient\Responses\DevPingResponse
     *
     * @uses \Example\CommentsClient\Settings
     */
    public function testRequiredHeadersSending(): void
    {
        $this->guzzleHandler->onUriRegexpRequested(
            '~' . preg_quote($this->settings->getBaseUri() . 'dev/ping', '/') . '.*~i',
            'get',
            $response = new Response(
                200, ['content-type' => 'application/json;charset=utf-8'], json_encode(
                       [
                           'value' => (string)time(),
                           'in' => $this->faker->numberBetween(0, 100),
                           'out' => $out = (time() * 1000),
                           'delay' => $out + 1,
                       ],
                       JSON_THROW_ON_ERROR,
                       512
                   )
            ),
            true
        );

        $this->client->devPing();

        $this->assertSame('https://dummy.server/api/v1/', $this->client->getSettings()->getBaseUri());

        $this->assertMatchesRegularExpression(
            '~.+curl\/\d.+PHP\/\d.+~',
            $this->guzzleHandler->getLastRequest()->getHeaderLine('User-Agent')
        );
    }

    /**
     * @return void
     * @uses   \Example\CommentsClient\Settings
     * @covers \Example\CommentsClient\Responses\DevPingResponse
     *
     */
    public function testDevPing(): void
    {
        $this->guzzleHandler->onUriRequested(
            $this->settings->getBaseUri() . sprintf('dev/ping?value=%d', $time = time()),
            'get',
            new Response(
                200, ['content-type' => 'application/json;charset=utf-8'], $raw = json_encode(
                [
                    'value' => $value = (string)$time,
                    'in' => $in = $this->faker->numberBetween(0, 100),
                    'out' => $out = ($time * 1000),
                    'delay' => $delay = ($out + 1),
                ],
                JSON_THROW_ON_ERROR,
                512
            )
            )
        );

        $response = $this->client->devPing($value);

        $this->assertSame($value, $response->getValue());
        $this->assertSame($in, $response->getIn());
        $this->assertSame($out, $response->getOut());
        $this->assertSame($delay, $response->getDelay());

        $this->assertJsonStringEqualsJsonString($raw, $response->getRawResponseContent());
    }

    /**
     * @return void
     * @uses   \Example\CommentsClient\Exceptions\BadResponseException
     * @covers \Example\CommentsClient\Responses\DevPingResponse
     *
     * @uses   \Example\CommentsClient\Settings
     */
    public function testDevPingUsingWrongJsonResponse(): void
    {
        $this->expectException(BadResponseException::class);

        $this->guzzleHandler->onUriRequested(
            $this->settings->getBaseUri() . sprintf('dev/ping?value=%d', $time = time()),
            'get',
            new Response(200, ['content-type' => 'application/json;charset=utf-8'], '{"foo":]')
        );

        $this->client->devPing();
    }

    /**
     * @return void
     * @uses   \Example\CommentsClient\Types\CommentType
     * @covers \Example\CommentsClient\Responses\CommentsResponse
     *
     * @uses   \Example\CommentsClient\Settings
     */
    public function testGetComments(): void
    {
        $this->guzzleHandler->onUriRequested(
            $this->settings->getBaseUri() . 'comments',
            'get',
            new Response(
                200, ['content-type' => 'application/json;charset=utf-8'],
                $raw = file_get_contents(__DIR__ . '/../stubs/getComments.json')
            )
        );

        $response = $this->client->getComments();

        foreach ($response as $item) {
            $this->assertInstanceOf(CommentType::class, $item);
        }

        $this->assertCount(2, $response->getData());

        $comment = $response->getData()[0];

        $this->assertSame(1, $comment->getId());
        $this->assertSame('Название комментария 1', $comment->getName());
        $this->assertSame('Комментарий 1', $comment->getText());

        $this->assertJsonStringEqualsJsonString($raw, $response->getRawResponseContent());
    }

    /**
     * @return void
     * @uses   \Example\CommentsClient\Exceptions\BadResponseException
     * @covers \Example\CommentsClient\Responses\CommentsResponse
     *
     * @uses   \Example\CommentsClient\Settings
     */
    public function testGetCommentsUsingWrongJsonResponse(): void
    {
        $this->expectException(BadResponseException::class);

        $this->guzzleHandler->onUriRequested(
            $this->settings->getBaseUri() . 'comments',
            'get',
            new Response(200, ['content-type' => 'application/json;charset=utf-8'], '{"foo":]')
        );

        $this->client->getComments();
    }

    /**
     * @return void
     * @uses   \Example\CommentsClient\Types\CommentType
     * @covers \Example\CommentsClient\Responses\CommentsResponse
     *
     * @uses   \Example\CommentsClient\Settings
     */
    public function testAddComment(): void
    {
        $this->guzzleHandler->onUriRequested(
            $this->settings->getBaseUri() . 'comment',
            'post',
            new Response(
                200, ['content-type' => 'application/json;charset=utf-8'],
                $raw = file_get_contents(__DIR__ . '/../stubs/addComment.json')
            )
        );

        $response = $this->client->createComment('Название комментария 3', 'Комментарий 3');

        $this->assertCount(1, $response->getData());

        $comment = $response->getData()[0];

        $this->assertInstanceOf(CommentType::class, $comment);
        $this->assertSame(3, $comment->getId());
        $this->assertSame('Название комментария 3', $comment->getName());
        $this->assertSame('Комментарий 3', $comment->getText());

        $this->assertJsonStringEqualsJsonString($raw, $response->getRawResponseContent());
    }

    /**
     * @return void
     * @uses   \Example\CommentsClient\Exceptions\BadResponseException
     * @covers \Example\CommentsClient\Responses\CommentsResponse
     *
     * @uses   \Example\CommentsClient\Settings
     */
    public function testAddCommentUsingWrongJsonResponse(): void
    {
        $this->expectException(BadResponseException::class);

        $this->guzzleHandler->onUriRequested(
            $this->settings->getBaseUri() . 'comment',
            'post',
            new Response(200, ['content-type' => 'application/json;charset=utf-8'], '{"foo":]')
        );

        $this->client->createComment('Название комментария 3', 'Комменатрий 3');
    }

    /**
     * @return void
     * @uses \Example\CommentsClient\Settings
     *
     */
    public function testAddCommentWithInvalidParameters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('~Missing~i');

        $this->client->createComment('Название комментария 3', '');
    }

    /**
     * @return void
     * @uses   \Example\CommentsClient\Types\CommentType
     * @covers \Example\CommentsClient\Responses\CommentsResponse
     *
     * @uses   \Example\CommentsClient\Settings
     */
    public function testUpdateComment(): void
    {
        $this->guzzleHandler->onUriRequested(
            $this->settings->getBaseUri() . 'comment/1',
            'put',
            new Response(
                200, ['content-type' => 'application/json;charset=utf-8'],
                $raw = file_get_contents(__DIR__ . '/../stubs/updateComment.json')
            )
        );

        $response = $this->client->updateComment(1, 'Новое название комментария 1', 'Комментарий 1');

        $this->assertCount(1, $response->getData());

        $comment = $response->getData()[0];

        $this->assertInstanceOf(CommentType::class, $comment);
        $this->assertSame(1, $comment->getId());
        $this->assertSame('Новое название комментария 1', $comment->getName());
        $this->assertSame('Комментарий 1', $comment->getText());

        $this->assertJsonStringEqualsJsonString($raw, $response->getRawResponseContent());
    }

    /**
     * @return void
     * @uses   \Example\CommentsClient\Exceptions\BadResponseException
     * @covers \Example\CommentsClient\Responses\CommentsResponse
     *
     * @uses   \Example\CommentsClient\Settings
     */
    public function testUpdateCommentUsingWrongJsonResponse(): void
    {
        $this->expectException(BadResponseException::class);

        $this->guzzleHandler->onUriRequested(
            $this->settings->getBaseUri() . 'comment/1',
            'put',
            new Response(200, ['content-type' => 'application/json;charset=utf-8'], '{"foo":]')
        );

        $this->client->updateComment(1, 'Новое название комментария 1', 'Комменатрий 1');
    }

    /**
     * @return void
     * @uses \Example\CommentsClient\Settings
     *
     */
    public function testUpdateCommentWithInvalidParameters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('~Missing~i');

        $this->client->updateComment(1, null, null);
    }
}
