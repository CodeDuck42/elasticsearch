<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use CodeDuck\Elasticsearch\Document;
use CodeDuck\Elasticsearch\Identifier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\Action\Index
 */
class IndexTest extends TestCase
{
    public function test(): void
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
            $action->jsonSerialize()
        );
        self::assertEquals(['foo' => 'bar'], $action->getDocument());
    }
}
