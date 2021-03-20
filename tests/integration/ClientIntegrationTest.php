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
        $client = new Client(HttpClient::create(), 'http://127.0.0.1:9200');
        $client->bulkAction(
            [
                new Index('11111', ['name' => 'example'], 'test-index'),
                new Index('22222', ['name' => 'banana'], 'test-index'),
            ]
        );

        $action = new Query(['query' => ['term' => ['name' => 'banana']]], 'test-index');

        self::assertEquals(['name' => 'banana'], $client->query($action));
    }
}
