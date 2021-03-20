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
        $action = new Delete('index', 'ABC123', 'document');

        self::assertEquals(
            [
                'delete' => [
                    '_id' => 'ABC123',
                    '_type' => 'document',
                    '_index' => 'index',
                ],
            ],
            $action->jsonSerialize()
        );
        self::assertNull($action->getDocument());
    }
}
