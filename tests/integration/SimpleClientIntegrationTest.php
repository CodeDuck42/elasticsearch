<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Exceptions\TransportException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class SimpleClientIntegrationTest extends TestCase
{
    private SimpleClient $sut;

    public function testBulkDeleteWithNoneExistingDocument(): void
    {
        $this->sut->begin();
        $this->sut->delete('TBDWNED');
        $this->sut->commit();

        self::assertTrue(true);
    }

    public function testDeleteWithExistingDocument(): void
    {
        $this->sut->add('TDWED', ['name' => 'example']);
        sleep(5); // wait for index
        $this->sut->delete('TDWED');

        self::assertTrue(true);
    }

    public function testDeleteWithNoneExistingDocument(): void
    {
        $this->expectException(TransportException::class);

        $this->sut->delete('TDWNED');
    }

    public function testIndex(): void
    {
        $this->sut->add('11111', ['name' => 'example']);

        self::assertTrue(true);
    }

    public function testQuery(): void
    {
        $this->sut->begin();
        $this->sut->add('11111', ['name' => 'example']);
        $this->sut->add('22222', ['name' => 'banana']);
        $this->sut->commit();

        sleep(5); // wait for index

        $result = $this->sut->query(['query' => ['term' => ['name' => 'banana']]]);

        self::assertEquals(1, $result->getCount());
        self::assertEquals(['name' => 'banana'], $result->getDocuments()[0]->getSource());
    }

    protected function setUp(): void
    {
        $this->sut = new SimpleClient(new Client(HttpClient::create(), 'http://localhost:9200'), 'test-index');
    }
}
