<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\ValueObjects;

use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\ValueObjects\QueryResult
 */
class QueryResultTest extends TestCase
{
    public function test(): void
    {
        $result = QueryResult::fromArray(
            [
                'took' => 822,
                'hits' => [
                    'max_score' => 0.6931471,
                    'hits' => [
                        0 => [
                            '_index' => 'test-index',
                            '_type' => '_doc',
                            '_id' => '22222',
                            '_score' => 0.6931471,
                            '_source' => [
                                'name' => 'banana',
                            ],
                        ],
                    ],
                ],
            ]
        );

        self::assertEquals(822, $result->getTook());
        self::assertEquals(0.6931471, $result->getMaxScore());
        self::assertEquals(1, $result->getCount());

        foreach ($result->getDocuments() as $document) {
            self::assertEquals('test-index', $document->getIdentifier()->getIndex());
            self::assertEquals('_doc', $document->getIdentifier()->getType());
            self::assertEquals('22222', $document->getIdentifier()->getId());
            self::assertEquals(0.6931471, $document->getScore());
            self::assertEquals(['name' => 'banana'], $document->getSource());
        }
    }

    public function testBrokenResults(): void
    {
        $result = QueryResult::fromArray([]);

        self::assertEquals(0, $result->getTook());
        self::assertEquals(0.0, $result->getMaxScore());
        self::assertEquals(0, $result->getCount());
    }
}
