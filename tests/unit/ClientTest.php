<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

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
    public function test(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Index(new Document(new Identifier('index', '123'), []));
        $request = $action->getRequest();

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                $request->getMethod(),
                $url.$request->getAbsolutePath(),
                ['body' => $request->getBody(), 'headers' => $request->getHeaders()]
            );

        $client = new Client($httpClient, $url);
        $client->execute($action);
    }

    public function _testBrokenRequestDocument(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Index(new Document(new Identifier('index', '123'), ['broken' => tmpfile()]));

        $this->expectException(ElasticsearchDataCouldNotBeEncodedException::class);

        $client = new Client($this->createMock(HttpClientInterface::class), $url);
        $client->execute($action);
    }

    public function _testBrokenResponseDocument(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Query([], 'index');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willThrowException($this->createMock(DecodingExceptionInterface::class));

        $this->expectException(ElasticsearchDataCouldNotBeDecodedException::class);

        $client = new Client($httpClient, $url);
        $client->query($action);
    }

    public function _testHttpError(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Query([], 'index');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willThrowException($this->createMock(TransportExceptionInterface::class));

        $this->expectException(ElasticsearchTransportException::class);

        $client = new Client($httpClient, $url);
        $client->query($action);
    }

    public function testQuery(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Query(['query' => ['term' => ['user.id' => 'kimchy']]], 'index');
        $request = $action->getRequest();

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                $request->getMethod(),
                $url.$request->getAbsolutePath(),
                ['body' => $request->getBody(), 'headers' => $request->getHeaders()]
            );

        $client = new Client($httpClient, $url);
        $client->query($action);
    }
}
