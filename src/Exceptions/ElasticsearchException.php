<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Exceptions;

use RuntimeException;
use Throwable;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ElasticsearchException extends RuntimeException
{
    public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
