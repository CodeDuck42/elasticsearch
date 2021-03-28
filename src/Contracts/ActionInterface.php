<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Contracts;

use CodeDuck\Elasticsearch\ValueObjects\Request;

/**
 * @psalm-immutable
 */
interface ActionInterface
{
    public function getRequest(): Request;
}
