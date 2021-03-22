<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

interface BulkActionInterface extends ActionInterface
{
    public function getBulkAction(): ?array;
}
