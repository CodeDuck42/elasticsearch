<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Exception;

use Throwable;

/**
 * @codeCoverageIgnore
 */
final class ElasticsearchTransportException extends ElasticsearchException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Something went wrong during with the http request.', $previous);
    }
}
