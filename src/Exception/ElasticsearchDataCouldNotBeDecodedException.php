<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Exception;

use Throwable;

/**
 * @codeCoverageIgnore
 */
final class ElasticsearchDataCouldNotBeDecodedException extends ElasticsearchException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Data could not be decoded.', $previous);
    }
}
