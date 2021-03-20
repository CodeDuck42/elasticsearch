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
        sleep(120); // wait for container startup if ci is slow

        $client = new Client(HttpClient::create(), 'http://localhost:9200');
        $client->bulkAction(
            [
                new Index('11111', ['name' => 'example'], 'test-index'),
                new Index('22222', ['name' => 'banana'], 'test-index'),
            ]
        );

        $action = new Query(['query' => ['term' => ['name' => 'banana']]], 'test-index');
        $result = $client->query($action);

        echo json_encode($result, JSON_PRETTY_PRINT);

        self::assertEquals(['name' => 'banana'], $result['hits']['hits'][0]['_source']);
    }
}
