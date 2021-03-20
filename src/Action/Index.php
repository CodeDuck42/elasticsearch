<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

/**
 * @psalm-immutable
 */
final class Index implements ActionInterface
{
    private array $document;

    public function __construct(string $id, array $data, string $index, string $type = '_doc')
    {
        $this->document = [
            '_id' => $id,
            '_type' => $type,
            '_index' => $index,
            '_source' => $data,
        ];
    }

    public function getActionType(): string
    {
        return 'index';
    }

    public function jsonSerialize(): array
    {
        return $this->document;
    }
}
