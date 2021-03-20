<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use CodeDuck\Elasticsearch\Identifier;

/**
 * @psalm-immutable
 */
final class Delete implements ActionInterface
{
    private Identifier $identifier;

    public function __construct(Identifier $identifier)
    {
        $this->identifier = $identifier;
    }

    public function jsonSerialize(): array
    {
        return [
            'delete' => [
                '_id' => $this->identifier->getId(),
                '_type' => $this->identifier->getType(),
                '_index' => $this->identifier->getIndex(),
            ],
        ];
    }

    public function getDocument(): ?array
    {
        return null;
    }
}
