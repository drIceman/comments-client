<?php

declare(strict_types=1);

namespace Example\CommentsClient\Test;

use Faker\Factory;
use Faker\Generator as Faker;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tarampampam\GuzzleUrlMock\UrlsMockHandler;

abstract class AbstractTestCase extends TestCase
{
    protected Faker $faker;
    protected UrlsMockHandler $guzzleHandler;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->guzzleHandler = new UrlsMockHandler();

        // Настройка ответов по умолчанию
        foreach (['get', 'post', 'put', 'delete', 'head', 'update'] as $method) {
            $this->guzzleHandler->onUriRegexpRequested(
                "~(?'{$method}').*~iu",
                $method,
                new Response(
                    404, [], 'Response mocked for testing'
                )
            );
        }
    }
}
