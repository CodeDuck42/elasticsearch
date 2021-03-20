<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

/**
 * @psalm-immutable
 */
final class Delete implements ActionInterface
{
    private array $action;

    public function __construct(string $index, string $id, string $type = '_doc')
    {
        $this->action = [
            'delete' => [
                '_id' => $id,
                '_type' => $type,
                '_index' => $index,
            ],
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->action;
    }

    public function getDocument(): ?array
    {
        return null;
    }
}
