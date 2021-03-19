<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Exception;

use Throwable;

final class ElasticsearchDataCouldNotBeEncodedException extends ElasticsearchException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Data could not be encoded', $previous);
    }
}
