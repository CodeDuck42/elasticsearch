<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Exceptions;

use Throwable;

/**
 * @codeCoverageIgnore
 */
final class DataCouldNotBeDecodedException extends ElasticsearchException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Data could not be decoded.', $previous);
    }
}
