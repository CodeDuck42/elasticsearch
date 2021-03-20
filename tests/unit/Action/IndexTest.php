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
        $action = new Index('ABC123', ['foo' => 'bar'], 'index', 'document');

        self::assertEquals(
            [
                '_id' => 'ABC123',
                '_type' => 'document',
                '_index' => 'index',
                '_source' => ['foo' => 'bar'],
            ],
            $action->jsonSerialize()
        );
        self::assertEquals('index', $action->getActionType());
    }
}
