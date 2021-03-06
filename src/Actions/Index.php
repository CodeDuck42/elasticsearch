<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Actions;

use CodeDuck\Elasticsearch\Contracts\BulkActionInterface;
use CodeDuck\Elasticsearch\Exceptions\DataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\ValueObjects\Document;
use CodeDuck\Elasticsearch\ValueObjects\Request;
use JsonException;

/**
 * @psalm-immutable
 */
final class Index implements BulkActionInterface
{
    private Document $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function getBulkAction(): ?array
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

    public function getRequest(): Request
    {
        return new Request(
            'PUT',
            sprintf(
                '/%s/%s/%s',
                $this->document->getIdentifier()->getIndex(),
                $this->document->getIdentifier()->getType(),
                $this->document->getIdentifier()->getId()
            ),
            $this->createBody(),
            ['Content-Type' => 'application/json']
        );
    }

    private function createBody(): string
    {
        try {
            return json_encode($this->document->getSource(), JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new DataCouldNotBeEncodedException($e);
        }
    }
}
