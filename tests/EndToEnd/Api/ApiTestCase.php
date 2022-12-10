<?php

declare(strict_types=1);

namespace Tests\EndToEnd\Api;

use App\Helper\JsonHelper;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use function json_encode;

abstract class ApiTestCase extends KernelTestCase
{
    protected KernelBrowser $client;

    public static function assertJsonResponse(int $statusCode, array $body, Response $response): void
    {
        self::assertSame($statusCode, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeader('Content-Type'));
        self::assertSame(
            $body,
            JsonHelper::decode($response->getContent()),
        );
    }

    public function setUp(): void
    {
        $this->client = self::getContainer()->get('test.client');
    }

    /**
     * @param string $uri
     * @param array $body
     * @param string|null $contentType
     * @param string|null $accept
     *
     * @return array{crawler: Crawler, response: Response}
     */
    public function post(
        string $uri,
        array $body,
        ?string $contentType = 'application/json',
        ?string $accept = 'application/json'
    ): array {
        $server = [];
        if ($contentType !== null) {
            $server['CONTENT_TYPE'] = $contentType;
        }

        if ($accept !== null) {
            $server['HTTP_ACCEPT'] = $accept;
        }

        return [
            'crawler' => $this->client->request('POST', $uri, [], [], $server, json_encode($body)),
            'response' => $this->client->getInternalResponse(),
        ];
    }
}
