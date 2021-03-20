<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\Action\Delete
 */
class DeleteTest extends TestCase
{
    public function test(): void
    {
        $action = new Delete('ABC123', 'index', 'document');

        self::assertEquals(
            [
                '_id' => 'ABC123',
                '_type' => 'document',
                '_index' => 'index',
            ],
            $action->jsonSerialize()
        );
        self::assertEquals('delete', $action->getActionType());
    }
}
