<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Actions;

use CodeDuck\Elasticsearch\Contracts\ActionInterface;
use CodeDuck\Elasticsearch\Contracts\BulkActionInterface;
use CodeDuck\Elasticsearch\Exceptions\DataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\ValueObjects\Request;
use JsonException;

/**
 * @psalm-immutable
 */
final class Bulk implements ActionInterface
{
    /**
     * @var BulkActionInterface[]
     * @psalm-readonly
     */
    private array $actions;

    public function __construct(BulkActionInterface ...$actions)
    {
        $this->actions = $actions;
    }

    public function getRequest(): Request
    {
        return new Request('POST', '/_bulk', $this->createBody(), ['Content-Type' => 'application/x-ndjson']);
    }

    private function createBody(): string
    {
        try {
            $body = '';

            foreach ($this->actions as $action) {
                $body .= sprintf("%s\n", json_encode($action->getBulkAction(), JSON_THROW_ON_ERROR));
                $actionBody = $action->getRequest()->getBody();

                if ($actionBody !== null) {
                    $body .= sprintf("%s\n", $actionBody);
                }
            }
        } catch (JsonException $e) {
            throw new DataCouldNotBeEncodedException($e);
        }

        return $body;
    }
}
