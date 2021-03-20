<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Action\Index;
use CodeDuck\Elasticsearch\Action\Query;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class ClientIntegrationTest extends TestCase
{
    public function testQuery(): void
    {
        $client = new Client(HttpClient::create(), 'http://localhost:9200');
        $client->bulkAction(
            [
                new Index(new Document(new Identifier('test-index', '11111'), ['name' => 'example'])),
                new Index(new Document(new Identifier('test-index', '22222'), ['name' => 'banana'])),
            ]
        );

        sleep(5); // wait for index

        $action = new Query(['query' => ['term' => ['name' => 'banana']]], 'test-index');
        $result = $client->query($action);

        self::assertEquals(1, $result->getCount());
        self::assertEquals(['name' => 'banana'], $result->getDocuments()[0]->getSource());
    }
}
