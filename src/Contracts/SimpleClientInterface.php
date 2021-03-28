<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Contracts;

use CodeDuck\Elasticsearch\ValueObjects\QueryResult;

interface SimpleClientInterface
{
    /**
     * Adds a document to elasticsearch.
     */
    public function add(string $id, array $data): void;

    /**
     * Activates bulk processing for all following add and delete calls
     */
    public function begin(): void;

    /**
     * Sends all add and delete calls since the begin call to elasticsearch and closes the bulk processing
     */
    public function commit(): void;

    /**
     * Deletes a document from elasticsearch
     */
    public function delete(string $id): void;

    /**
     * Runs a query on the active index
     */
    public function query(array $query): QueryResult;

    /**
     * Clears all add and delete calls since the begin call and closes the bulk processing
     */
    public function rollBack(): void;
}
