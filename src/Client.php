<?php

/*
 * CONNOX GMBH CONFIDENTIAL
 * ________________________
 *
 * Copyright (c) 2005 - 2021 Connox GmbH, All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains the property
 * of Connox GmbH and its suppliers, if any. The intellectual and
 * technical concepts contained herein are proprietary to Connox GmbH and
 * its suppliers and may be covered by German Laws and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material is
 * strictly forbidden unless prior written permission is obtained from
 * Connox GmbH.
 */

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Action\ActionInterface;
use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\Exception\ElasticsearchException;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private HttpClientInterface $httpClient;
    private string $elasticsearchUrl;

    public function __construct(HttpClientInterface $httpClient, string $elasticsearchUrl)
    {
        $this->httpClient = $httpClient;
        $this->elasticsearchUrl = rtrim($elasticsearchUrl, '/');
    }

    public function action(ActionInterface $action): void
    {
        $this->bulkAction([$action]);
    }

    /**
     * @param ActionInterface[] $actions
     */
    public function bulkAction(array $actions): void
    {
        try {
            $request = '';

            foreach ($actions as $action) {
                $request .= sprintf("%s\n", json_encode($action, JSON_THROW_ON_ERROR));
            }
        } catch (JsonException $e) {
            throw new ElasticsearchDataCouldNotBeEncodedException($e);
        }

        $this->request('POST', '_bulk', $request);
    }

    public function query(): array
    {
        return [];
    }

    private function request(string $method, string $path, string $data): void
    {
        try {
            $this->httpClient->request($method, $this->elasticsearchUrl . $path, ['body' => $data]);
        } catch (TransportExceptionInterface $e) {
            throw new ElasticsearchException($e->getMessage(), $e);
        }
    }
}
