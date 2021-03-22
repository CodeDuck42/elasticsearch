<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeEncodedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\Action\Query
 */
class QueryTest extends TestCase
{
    public function test(): void
    {
        $action = new Query(['foo' => 'bar'], 'index');
        $request = $action->getRequest();

        self::assertEquals('GET', $request->getMethod());
        self::assertEquals('/index/_search', $request->getAbsolutePath());
        self::assertEquals(['Content-Type' => 'application/json'], $request->getHeaders());
        self::assertEquals('{"foo":"bar"}', $request->getBody());
    }

    public function testEncodingError(): void
    {
        $this->expectException(ElasticsearchDataCouldNotBeEncodedException::class);

        $action = new Query(['broken' => tmpfile()], 'index');
        $action->getRequest();
    }
}
