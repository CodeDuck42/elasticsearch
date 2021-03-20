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
use CodeDuck\Elasticsearch\Action\Query;
use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeDecodedException;
use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeEncodedException;
use CodeDuck\Elasticsearch\Exception\ElasticsearchTransportException;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
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

        $this->request('POST', '/_bulk', ['body' => $request]);
    }

    public function query(Query $query): array
    {
        return $this->request('GET', sprintf('/%s/_search', $query->getIndex()), ['json' => $query]);
    }

    private function request(string $method, string $path, array $options): array
    {
        try {
            return $this->httpClient->request($method, $this->elasticsearchUrl.$path, $options)->toArray();
        } catch (TransportExceptionInterface | HttpExceptionInterface $e) {
            throw new ElasticsearchTransportException($e);
        } catch (DecodingExceptionInterface $e) {
            throw new ElasticsearchDataCouldNotBeDecodedException($e);
        }
    }
}
