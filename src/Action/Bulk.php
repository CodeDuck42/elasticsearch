<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\Request;
use JsonException;

final class Bulk implements ActionInterface
{
    /** @var BulkActionInterface[] */
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
            throw new ElasticsearchDataCouldNotBeEncodedException($e);
        }

        return $body;
    }
}
