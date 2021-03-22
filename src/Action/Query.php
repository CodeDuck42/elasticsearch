<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\Request;
use JsonException;

/**
 * @psalm-immutable
 */
final class Query implements ActionInterface
{
    private string $index;
    private array $query;

    public function __construct(array $queryStatement, string $index = '_all')
    {
        $this->query = $queryStatement;
        $this->index = $index;
    }

    public function getRequest(): Request
    {
        return new Request(
            'GET',
            sprintf('/%s/_search', $this->index),
            $this->createBody(),
            ['Content-Type' => 'application/json']
        );
    }

    private function createBody(): string
    {
        try {
            return json_encode($this->query, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ElasticsearchDataCouldNotBeEncodedException($e);
        }
    }
}
