<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Action\ActionInterface;
use CodeDuck\Elasticsearch\Action\Query;
use CodeDuck\Elasticsearch\Exception\ElasticsearchDataCouldNotBeDecodedException;
use CodeDuck\Elasticsearch\Exception\ElasticsearchTransportException;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private HttpClientInterface $httpClient;
    private string $url;

    public function __construct(HttpClientInterface $httpClient, string $url)
    {
        $this->httpClient = $httpClient;
        $this->url = rtrim($url, '/');
    }

    public function execute(ActionInterface $action): void
    {
        $this->request($action->getRequest());
    }

    public function query(Query $query): QueryResult
    {
        return QueryResult::fromArray($this->request($query->getRequest()));
    }

    private function request(Request $request): array
    {
        try {
            return $this->httpClient->request(
                $request->getMethod(),
                $this->url.$request->getAbsolutePath(),
                [
                    'body' => $request->getBody(),
                    'headers' => $request->getHeaders(),
                ]
            )->toArray();
        } catch (TransportExceptionInterface | HttpExceptionInterface $e) {
            throw new ElasticsearchTransportException($e);
        } catch (DecodingExceptionInterface $e) {
            throw new ElasticsearchDataCouldNotBeDecodedException($e);
        }
    }
}
