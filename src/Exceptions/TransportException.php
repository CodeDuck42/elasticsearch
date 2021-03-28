<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Exceptions;

use Throwable;

/**
 * @codeCoverageIgnore
 */
final class TransportException extends ElasticsearchException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Something went wrong during with the http request.', $previous);
    }
}
