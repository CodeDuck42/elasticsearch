<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

/**
 * @psalm-immutable
 */
final class Delete implements ActionInterface
{
    private array $document;

    public function __construct(string $id, string $index, string $type = '_doc')
    {
        $this->document = [
            '_id' => $id,
            '_type' => $type,
            '_index' => $index,
        ];
    }

    public function getActionType(): string
    {
        return 'delete';
    }

    public function jsonSerialize(): array
    {
        return $this->document;
    }
}
