<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

/**
 * @psalm-immutable
 */
final class Index implements ActionInterface
{
    private array $action;
    private array $document;

    public function __construct(string $id, array $document, string $index, string $type = '_doc')
    {
        $this->action = [
            'index' => [
                '_id' => $id,
                '_type' => $type,
                '_index' => $index,
            ],
        ];

        $this->document = $document;
    }

    public function jsonSerialize(): array
    {
        return $this->action;
    }

    public function getDocument(): ?array
    {
        return $this->document;
    }
}
