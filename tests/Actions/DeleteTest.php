<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Actions;

use CodeDuck\Elasticsearch\ValueObjects\Identifier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\Actions\Delete
 */
class DeleteTest extends TestCase
{
    public function test(): void
    {
        $action = new Delete(new Identifier('index', 'ABC123', 'document'));
        $request = $action->getRequest();

        self::assertEquals('DELETE', $request->getMethod());
        self::assertEquals('/index/document/ABC123', $request->getAbsolutePath());
        self::assertEquals([], $request->getHeaders());
        self::assertNull($request->getBody());
    }

    public function testBulkAction(): void
    {
        $action = new Delete(new Identifier('index', 'ABC123', 'document'));

        self::assertEquals(
            [
                'delete' => [
                    '_id' => 'ABC123',
                    '_type' => 'document',
                    '_index' => 'index',
                ],
            ],
            $action->getBulkAction()
        );
    }
}
