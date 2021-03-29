<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Actions\Bulk;
use CodeDuck\Elasticsearch\Actions\Delete;
use CodeDuck\Elasticsearch\Actions\Index;
use CodeDuck\Elasticsearch\Actions\Query;
use CodeDuck\Elasticsearch\Exceptions\DataCouldNotBeDecodedException;
use CodeDuck\Elasticsearch\Exceptions\DataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\Exceptions\TransportException;
use CodeDuck\Elasticsearch\ValueObjects\Document;
use CodeDuck\Elasticsearch\ValueObjects\Identifier;
use CodeDuck\Elasticsearch\ValueObjects\QueryResult;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
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

        $sut = new Client($httpClient, $url);
        $result = $sut->execute($action);

        self::assertNull($result);
    }

    public function testBrokenRequestDocument(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Index(new Document(new Identifier('index', '123'), ['broken' => tmpfile()]));

        $this->expectException(DataCouldNotBeEncodedException::class);

        $sut = new Client($this->createMock(HttpClientInterface::class), $url);
        $sut->execute($action);
    }

    public function testBrokenResponseDocument(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Query([], 'index');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willThrowException($this->createMock(DecodingExceptionInterface::class));

        $this->expectException(DataCouldNotBeDecodedException::class);

        $sut = new Client($httpClient, $url);
        $sut->execute($action);
    }

    public function testHttpError(): void
    {
        $url = 'https://127.0.0.1';
        $action = new Query([], 'index');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willThrowException($this->createMock(TransportExceptionInterface::class));

        $this->expectException(TransportException::class);

        $sut = new Client($httpClient, $url);
        $sut->execute($action);
    }

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerBulkDeletingMissingDocumentsShouldWork(): void
    {
        $identifier = new Identifier('test-index', 'TBDWNED');

        $sut = new Client(HttpClient::create(), 'http://localhost:9200');
        $sut->execute(new Bulk(new Delete($identifier)));

        self::assertTrue(true);
    }

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerDeletingAMissingDocumentShouldThrowTransportException(): void
    {
        $this->expectException(TransportException::class);

        $sut = new Client(HttpClient::create(), 'http://localhost:9200');
        $sut->execute(new Delete(new Identifier('test-index', 'TDWNED')));
    }

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerDeletingAnExistingDocumentShouldWork(): void
    {
        $identifier = new Identifier('test-index', 'TDWED');

        $sut = new Client(HttpClient::create(), 'http://localhost:9200');
        $sut->execute(new Index(new Document($identifier, ['name' => 'example'])));
        sleep(5); // wait for index
        $sut->execute(new Delete($identifier));

        self::assertTrue(true);
    }

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerIndexingADocumentShouldWork(): void
    {
        $sut = new Client(HttpClient::create(), 'http://localhost:9200');
        $sut->execute(new Index(new Document(new Identifier('test-index', '11111'), ['name' => 'example'])));

        self::assertTrue(true);
    }

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerRunningASearchQueryShouldReturnAMatchingResult(): void
    {
        $sut = new Client(HttpClient::create(), 'http://localhost:9200');
        $sut->execute(
            new Bulk(
                new Index(new Document(new Identifier('test-index', '11111'), ['name' => 'example'])),
                new Index(new Document(new Identifier('test-index', '22222'), ['name' => 'banana'])),
            )
        );

        sleep(5); // wait for index

        $action = new Query(['query' => ['term' => ['name' => 'banana']]], 'test-index');
        $result = $sut->execute($action);

        self::assertInstanceOf(QueryResult::class, $result);
        self::assertEquals(1, $result->getCount());
        self::assertEquals(['name' => 'banana'], $result->getDocuments()[0]->getSource());
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

        $sut = new Client($httpClient, $url);
        $result = $sut->execute($action);

        self::assertInstanceOf(QueryResult::class, $result);
    }

    public function testServerAddressWithTrailingSlash(): void
    {
        $url = 'https://127.0.0.1/';
        $action = new Query([], 'index');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                'https://127.0.0.1/index/_search',
                ['body' => '[]', 'headers' => ['Content-Type' => 'application/json']]
            );

        $sut = new Client($httpClient, $url);
        $sut->execute($action);
    }
}
