<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use CodeDuck\Elasticsearch\Document;
use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\Identifier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\Action\Index
 */
class IndexTest extends TestCase
{
    public function test(): void
    {
        $action = new Index(new Document(new Identifier('index', 'ABC123', 'document'), ['foo' => 'bar']));
        $request = $action->getRequest();

        self::assertEquals('PUT', $request->getMethod());
        self::assertEquals('/index/document/ABC123', $request->getAbsolutePath());
        self::assertEquals(['Content-Type' => 'application/json'], $request->getHeaders());
        self::assertEquals('{"foo":"bar"}', $request->getBody());
    }

    public function testBulkAction(): void
    {
        $action = new Index(new Document(new Identifier('index', 'ABC123', 'document'), ['foo' => 'bar']));

        self::assertEquals(
            [
                'index' => [
                    '_id' => 'ABC123',
                    '_type' => 'document',
                    '_index' => 'index',
                ],
            ],
            $action->getBulkAction()
        );
    }

    public function testEncodingError(): void
    {
        $this->expectException(ElasticsearchDataCouldNotBeEncodedException::class);

        $action = new Index(new Document(new Identifier('index', 'ABC123', 'document'), ['broken' => tmpfile()]));
        $action->getRequest();
    }
}
