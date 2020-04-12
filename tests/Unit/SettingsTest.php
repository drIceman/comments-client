<?php

declare(strict_types=1);

namespace Example\CommentsClient\Tests;

use Example\CommentsClient\Settings;
use Example\CommentsClient\Test\AbstractTestCase;
use GuzzleHttp\RequestOptions as GuzzleHttpOptions;

/**
 * @covers \Example\CommentsClient\Settings
 */
class SettingsTest extends AbstractTestCase
{
    protected Settings $settings;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->settings = new Settings();
    }

    /**
     * @return void
     */
    public function testConstructorDefaults(): void
    {
        $this->assertSame('https://dummy.server/api/v1/', $this->settings->getBaseUri());

        $this->assertTrue($this->settings->getGuzzleOptions()[GuzzleHttpOptions::VERIFY]);
        $this->assertSame(60.0, $this->settings->getGuzzleOptions()[GuzzleHttpOptions::TIMEOUT]);
    }

    /**
     * @return void
     */
    public function testBaseUriSetter(): void
    {
        $this->settings = new Settings($uri = 'http://httpbin.org/foo');

        $this->assertSame($uri . '/', $this->settings->getBaseUri());
    }

    /**
     * @return void
     */
    public function testGuzzleOptionsSetter(): void
    {
        $this->settings = new Settings(
            null, [
            GuzzleHttpOptions::ALLOW_REDIRECTS => false,
        ]
        );

        $this->assertFalse($this->settings->getGuzzleOptions()[GuzzleHttpOptions::ALLOW_REDIRECTS]);

        $this->assertSame('https://dummy.server/api/v1/', $this->settings->getBaseUri());
    }
}
