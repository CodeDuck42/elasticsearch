<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use JsonSerializable;

interface ActionInterface extends JsonSerializable
{
    public function getDocument(): ?array;
}
