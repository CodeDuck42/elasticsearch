<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Actions;

use CodeDuck\Elasticsearch\Contracts\BulkActionInterface;
use CodeDuck\Elasticsearch\Exceptions\DataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\ValueObjects\Request;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\Actions\Bulk
 */
class BulkTest extends TestCase
{
    public function test(): void
    {
        $action1 = $this->createMock(BulkActionInterface::class);
        $action1->method('getBulkAction')->willReturn(['delete' => ['foo' => 'bar']]);
        $action1->method('getRequest')->willReturn(new Request('DELETE', '/test'));

        $action2 = $this->createMock(BulkActionInterface::class);
        $action2->method('getBulkAction')->willReturn(['index' => ['foo' => 'bar']]);
        $action2->method('getRequest')->willReturn(new Request('PUT', '/test', '{}'));

        $bulk = new Bulk($action1, $action2);
        $request = $bulk->getRequest();

        self::assertEquals(['Content-Type' => 'application/x-ndjson'], $request->getHeaders());
        self::assertEquals('/_bulk', $request->getAbsolutePath());
        self::assertEquals('POST', $request->getMethod());
        self::assertEquals("{\"delete\":{\"foo\":\"bar\"}}\n{\"index\":{\"foo\":\"bar\"}}\n{}\n", $request->getBody());
    }

    public function testEncodingError(): void
    {
        $action = $this->createMock(BulkActionInterface::class);
        $action->method('getBulkAction')->willReturn(['broken' => tmpfile()]);

        $this->expectException(DataCouldNotBeEncodedException::class);

        $bulk = new Bulk($action);
        $bulk->getRequest();
    }
}
