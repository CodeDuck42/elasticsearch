<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Actions\Bulk;
use CodeDuck\Elasticsearch\Actions\Delete;
use CodeDuck\Elasticsearch\Actions\Index;
use CodeDuck\Elasticsearch\Actions\Query;
use CodeDuck\Elasticsearch\Exceptions\TransportException;
use CodeDuck\Elasticsearch\ValueObjects\Document;
use CodeDuck\Elasticsearch\ValueObjects\Identifier;
use CodeDuck\Elasticsearch\ValueObjects\QueryResult;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class ClientIntegrationTest extends TestCase
{
    public function testBulkDeleteWithNoneExistingDocument(): void
    {
        $identifier = new Identifier('test-index', 'TBDWNED');

        $client = new Client(HttpClient::create(), 'http://localhost:9200');
        $client->execute(new Bulk(new Delete($identifier)));

        self::assertTrue(true);
    }

    public function testDeleteWithExistingDocument(): void
    {
        $identifier = new Identifier('test-index', 'TDWED');

        $client = new Client(HttpClient::create(), 'http://localhost:9200');
        $client->execute(new Index(new Document($identifier, ['name' => 'example'])));
        sleep(5); // wait for index
        $client->execute(new Delete($identifier));

        self::assertTrue(true);
    }

    public function testDeleteWithNoneExistingDocument(): void
    {
        $this->expectException(TransportException::class);

        $client = new Client(HttpClient::create(), 'http://localhost:9200');
        $client->execute(new Delete(new Identifier('test-index', 'TDWNED')));
    }

    public function testIndex(): void
    {
        $client = new Client(HttpClient::create(), 'http://localhost:9200');
        $client->execute(new Index(new Document(new Identifier('test-index', '11111'), ['name' => 'example'])));

        self::assertTrue(true);
    }

    public function testQuery(): void
    {
        $client = new Client(HttpClient::create(), 'http://localhost:9200');
        $client->execute(
            new Bulk(
                new Index(new Document(new Identifier('test-index', '11111'), ['name' => 'example'])),
                new Index(new Document(new Identifier('test-index', '22222'), ['name' => 'banana'])),
            )
        );

        sleep(5); // wait for index

        $action = new Query(['query' => ['term' => ['name' => 'banana']]], 'test-index');
        $result = $client->execute($action);

        self::assertInstanceOf(QueryResult::class, $result);
        self::assertEquals(1, $result->getCount());
        self::assertEquals(['name' => 'banana'], $result->getDocuments()[0]->getSource());
    }
}
