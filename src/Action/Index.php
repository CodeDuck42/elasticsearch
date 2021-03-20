<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use CodeDuck\Elasticsearch\Document;

/**
 * @psalm-immutable
 */
final class Index implements ActionInterface
{
    private Document $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function jsonSerialize(): array
    {
        $identifier = $this->document->getIdentifier();

        return [
            'index' => [
                '_id' => $identifier->getId(),
                '_type' => $identifier->getType(),
                '_index' => $identifier->getIndex(),
            ],
        ];
    }

    public function getDocument(): ?array
    {
        return $this->document->getSource();
    }
}
