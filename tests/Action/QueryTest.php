<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function test(): void
    {
        $action = new Query(['foo' => 'bar'], 'index');

        self::assertEquals(['foo' => 'bar'], $action->jsonSerialize());
        self::assertEquals('index', $action->getIndex());
        self::assertEquals('query', $action->getActionType());
    }
}
