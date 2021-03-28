<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Contracts\ActionInterface;
use CodeDuck\Elasticsearch\Contracts\ClientInterface;
use CodeDuck\Elasticsearch\Contracts\QueryActionInterface;
use CodeDuck\Elasticsearch\Exceptions\DataCouldNotBeDecodedException;
use CodeDuck\Elasticsearch\Exceptions\TransportException;
use CodeDuck\Elasticsearch\ValueObjects\QueryResult;
use CodeDuck\Elasticsearch\ValueObjects\Request;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client implements ClientInterface
{
    private HttpClientInterface $httpClient;
    private string $url;

    public function __construct(HttpClientInterface $httpClient, string $url)
    {
        $this->httpClient = $httpClient;
        $this->url = rtrim($url, '/');
    }

    public function execute(ActionInterface $action): ?QueryResult
    {
        $result = $this->request($action->getRequest());

        return $action instanceof QueryActionInterface ? QueryResult::fromArray($result) : null;
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
            throw new TransportException($e);
        } catch (DecodingExceptionInterface $e) {
            throw new DataCouldNotBeDecodedException($e);
        }
    }
}
