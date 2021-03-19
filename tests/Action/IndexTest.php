<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\Action\Index
 */
class IndexTest extends TestCase
{
    public function test(): void
    {
        $action = new Index('ABC123', ['foo' => 'bar'], 'index');

        self::assertEquals(
            [
                '_id' => 'ABC123',
                '_type' => '_doc',
                '_index' => 'index',
                '_source' => ['foo' => 'bar'],
            ],
            $action->jsonSerialize()
        );
    }

    public function testGetActionType(): void
    {
        $action = new Index('ABC123', ['foo' => 'bar'], 'index');

        self::assertEquals('index', $action->getActionType());
    }
}
