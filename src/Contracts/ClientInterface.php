<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Contracts;

use CodeDuck\Elasticsearch\ValueObjects\QueryResult;

interface ClientInterface
{
    /**
     * Runs an action on the elasticsearch server
     */
    public function execute(ActionInterface $action): ?QueryResult;
}
