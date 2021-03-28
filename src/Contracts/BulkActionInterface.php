<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Contracts;

/**
 * @psalm-immutable
 */
interface BulkActionInterface extends ActionInterface
{
    public function getBulkAction(): ?array;
}
