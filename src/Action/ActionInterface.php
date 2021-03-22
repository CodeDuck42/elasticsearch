<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use CodeDuck\Elasticsearch\Request;

interface ActionInterface
{
    public function getRequest(): Request;
}
