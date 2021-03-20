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
        $action = new Index('index', 'ABC123', ['foo' => 'bar'], 'document');

        self::assertEquals(
            [
                'index' => [
                    '_id' => 'ABC123',
                    '_type' => 'document',
                    '_index' => 'index',
                ],
            ],
            $action->jsonSerialize()
        );
        self::assertEquals(['foo' => 'bar'], $action->getDocument());
    }
}
