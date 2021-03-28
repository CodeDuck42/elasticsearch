<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Actions;

use CodeDuck\Elasticsearch\Exceptions\DataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\ValueObjects\Document;
use CodeDuck\Elasticsearch\ValueObjects\Identifier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\Actions\Index
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
        $this->expectException(DataCouldNotBeEncodedException::class);

        $action = new Index(new Document(new Identifier('index', 'ABC123', 'document'), ['broken' => tmpfile()]));
        $action->getRequest();
    }
}
