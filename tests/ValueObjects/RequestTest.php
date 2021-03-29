<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\ValueObjects;

use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\ValueObjects\Request
 */
class RequestTest extends TestCase
{
    public function test(): void
    {
        $request = new Request('ORIGIN', '/some/path', 'BODY', ['foo' => 'bar']);

        self::assertEquals('ORIGIN', $request->getMethod());
        self::assertEquals('/some/path', $request->getAbsolutePath());
        self::assertEquals('BODY', $request->getBody());
        self::assertEquals(['foo' => 'bar'], $request->getHeaders());
    }
}
