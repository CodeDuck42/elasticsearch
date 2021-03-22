<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use CodeDuck\Elasticsearch\Identifier;
use CodeDuck\Elasticsearch\Request;

/**
 * @psalm-immutable
 */
final class Delete implements BulkActionInterface
{
    private Identifier $identifier;

    public function __construct(Identifier $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getBulkAction(): array
    {
        return [
            'delete' => [
                '_id' => $this->identifier->getId(),
                '_type' => $this->identifier->getType(),
                '_index' => $this->identifier->getIndex(),
            ],
        ];
    }

    public function getRequest(): Request
    {
        return new Request(
            'DELETE',
            sprintf(
                '/%s/%s/%s',
                $this->identifier->getIndex(),
                $this->identifier->getType(),
                $this->identifier->getId()
            )
        );
    }
}
