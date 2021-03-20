<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

/**
 * @psalm-immutable
 */
final class Query implements ActionInterface
{
    private array $query;
    private string $index;

    public function __construct(array $queryStatement, string $index = '_all')
    {
        $this->query = $queryStatement;
        $this->index = $index;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function jsonSerialize(): array
    {
        return $this->query;
    }

    public function getDocument(): ?array
    {
        return null;
    }
}
