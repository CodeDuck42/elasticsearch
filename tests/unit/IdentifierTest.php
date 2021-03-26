<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\Identifier
 */
class IdentifierTest extends TestCase
{
    public function test(): void
    {
        $identifier = Identifier::fromArray(
            [
                '_index' => 'my-index',
                '_id' => 'ID123',
                '_type' => 'custom-type',
            ]
        );

        self::assertEquals('my-index', $identifier->getIndex());
        self::assertEquals('ID123', $identifier->getId());
        self::assertEquals('custom-type', $identifier->getType());
    }

    public function testBrokenArray(): void
    {
        $identifier = Identifier::fromArray([]);

        self::assertEquals('', $identifier->getIndex());
        self::assertEquals('', $identifier->getId());
        self::assertEquals('_doc', $identifier->getType());
    }
}
