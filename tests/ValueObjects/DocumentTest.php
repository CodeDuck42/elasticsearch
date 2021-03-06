<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\ValueObjects;

use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\ValueObjects\Document
 */
class DocumentTest extends TestCase
{
    public function test(): void
    {
        $document = new Document(
            Identifier::fromArray(['_index' => 'test-index', '_type' => '_doc', '_id' => '22222']),
            ['name' => 'banana']
        );

        self::assertEquals('test-index', $document->getIdentifier()->getIndex());
        self::assertEquals('_doc', $document->getIdentifier()->getType());
        self::assertEquals('22222', $document->getIdentifier()->getId());
        self::assertEquals(0.0, $document->getScore());
        self::assertEquals(['name' => 'banana'], $document->getSource());
    }

    public function testBrokenArray(): void
    {
        $document = Document::fromArray([]);

        self::assertEquals('', $document->getIdentifier()->getIndex());
        self::assertEquals('_doc', $document->getIdentifier()->getType());
        self::assertEquals('', $document->getIdentifier()->getId());
        self::assertEquals(0.0, $document->getScore());
        self::assertEquals([], $document->getSource());
    }

    public function testFromArray(): void
    {
        $document = Document::fromArray(
            [
                '_index' => 'test-index',
                '_type' => '_doc',
                '_id' => '22222',
                '_score' => 0.6931471,
                '_source' => [
                    'name' => 'banana',
                ],
            ]
        );

        self::assertEquals('test-index', $document->getIdentifier()->getIndex());
        self::assertEquals('_doc', $document->getIdentifier()->getType());
        self::assertEquals('22222', $document->getIdentifier()->getId());
        self::assertEquals(0.6931471, $document->getScore());
        self::assertEquals(['name' => 'banana'], $document->getSource());
    }
}
