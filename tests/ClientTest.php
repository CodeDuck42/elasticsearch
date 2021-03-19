<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Action\Index;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @covers \CodeDuck\Elasticsearch\Client
 */
class ClientTest extends TestCase
{
    public function testAction(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Index('123', [], 'index');
        $body = json_encode($action, JSON_THROW_ON_ERROR) . "\n";

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects(self::once())->method('request')->with('POST', $url.'/_bulk', ['body' => $body]);

        $client = new Client($httpClient, $url);
        $client->action($action);
    }

    public function testBulkAction(): void
    {
        $url = 'https://127.0.0.1';
        $actions = [new Index('10101', [], 'index'), new Index('22222', [], 'index')];
        $body = json_encode($actions[0], JSON_THROW_ON_ERROR)."\n".json_encode($actions[1], JSON_THROW_ON_ERROR) . "\n";

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects(self::once())->method('request')->with('POST', $url.'/_bulk', ['body' => $body]);

        $client = new Client($httpClient, $url);
        $client->bulkAction($actions);
    }

    public function testQuery(): void
    {
    }
}
