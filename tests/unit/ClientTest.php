<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Action\Delete;
use CodeDuck\Elasticsearch\Action\Index;
use CodeDuck\Elasticsearch\Action\Query;
use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeDecodedException;
use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\Exception\ElasticsearchTransportException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @covers \CodeDuck\Elasticsearch\Client
 */
class ClientTest extends TestCase
{
    public function testAction(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Index('index', '123', []);
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
        $actions = [new Delete('index', '10101'), new Delete('index', '22222')];
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

    public function testBrokenRequestDocument(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Index('index', '123', ['broken' => tmpfile()]);

        $this->expectException(ElasticsearchDataCouldNotBeEncodedException::class);

        $client = new Client($this->createMock(HttpClientInterface::class), $url);
        $client->action($action);
    }

    public function testBrokenResponseDocument(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Query([], 'index');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willThrowException($this->createMock(DecodingExceptionInterface::class));

        $this->expectException(ElasticsearchDataCouldNotBeDecodedException::class);

        $client = new Client($httpClient, $url);
        $client->query($action);
    }

    public function testHttpError(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Query([], 'index');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willThrowException($this->createMock(TransportExceptionInterface::class));

        $this->expectException(ElasticsearchTransportException::class);

        $client = new Client($httpClient, $url);
        $client->query($action);
    }
}
