<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Actions\Bulk;
use CodeDuck\Elasticsearch\Actions\Delete;
use CodeDuck\Elasticsearch\Actions\Index;
use CodeDuck\Elasticsearch\Actions\Query;
use CodeDuck\Elasticsearch\Contracts\ActionInterface;
use CodeDuck\Elasticsearch\Contracts\BulkActionInterface;
use CodeDuck\Elasticsearch\Contracts\ClientInterface;
use CodeDuck\Elasticsearch\Contracts\QueryActionInterface;
use CodeDuck\Elasticsearch\Contracts\SimpleClientInterface;
use CodeDuck\Elasticsearch\ValueObjects\Document;
use CodeDuck\Elasticsearch\ValueObjects\Identifier;
use CodeDuck\Elasticsearch\ValueObjects\QueryResult;

class SimpleClient implements SimpleClientInterface
{
    /** @var BulkActionInterface[] */
    private array $bulkActions = [];
    private ClientInterface $client;
    private string $index;
    private bool $isBulkActive = false;
    private string $type;

    public function __construct(ClientInterface $client, string $index, string $type = '_doc')
    {
        $this->client = $client;
        $this->index = $index;
        $this->type = $type;
    }

    public function add(string $id, array $data): void
    {
        $this->execute(new Index(new Document($this->createIdentifier($id), $data)));
    }

    public function begin(): void
    {
        $this->isBulkActive = true;
    }

    public function commit(): void
    {
        if (count($this->bulkActions) > 0) {
            $this->client->execute(new Bulk(...$this->bulkActions));
        }

        $this->bulkActions = [];
        $this->isBulkActive = false;
    }

    public function delete(string $id): void
    {
        $this->execute(new Delete($this->createIdentifier($id)));
    }

    public function query(array $query): QueryResult
    {
        return $this->execute(new Query($query, $this->index));
    }

    public function rollBack(): void
    {
        $this->bulkActions = [];
        $this->isBulkActive = false;
    }

    private function createIdentifier(string $id): Identifier
    {
        return new Identifier($this->index, $id, $this->type);
    }

    /**
     * @psalm-return ($action is QueryActionInterface ? QueryResult : null)
     */
    private function execute(ActionInterface $action): ?QueryResult
    {
        if ($action instanceof QueryActionInterface) {
            return $this->client->execute($action);
        }

        if ($this->isBulkActive && $action instanceof BulkActionInterface) {
            $this->bulkActions[] = $action;

            return null;
        }

        return $this->client->execute($action);
    }
}
