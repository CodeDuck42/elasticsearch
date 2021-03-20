<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Action\Delete;
use CodeDuck\Elasticsearch\Action\Index;
use CodeDuck\Elasticsearch\Action\Query;
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
        $body = json_encode($action, JSON_THROW_ON_ERROR)."\n";
        $body .= json_encode($action->getDocument(), JSON_THROW_ON_ERROR)."\n";

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with('POST', $url.'/_bulk', ['body' => $body, 'headers' => ['Content-Type' => 'application/x-ndjson']]);

        $client = new Client($httpClient, $url);
        $client->action($action);
    }

    public function testBulkAction(): void
    {
        $url = 'https://127.0.0.1';
        $actions = [new Delete('10101', 'index'), new Delete('22222', 'index')];
        $body = json_encode($actions[0], JSON_THROW_ON_ERROR)."\n".json_encode($actions[1], JSON_THROW_ON_ERROR)."\n";

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with('POST', $url.'/_bulk', ['body' => $body, 'headers' => ['Content-Type' => 'application/x-ndjson']]);

        $client = new Client($httpClient, $url);
        $client->bulkAction($actions);
    }

    public function testQuery(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Query(['query' => ['term' => ['user.id' => 'kimchy']]], 'index');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with('GET', $url.'/index/_search', ['json' => $action]);

        $client = new Client($httpClient, $url);
        $client->query($action);
    }
}
