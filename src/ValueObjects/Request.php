<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\ValueObjects;

/**
 * @internal
 * @psalm-immutable
 */
final class Request
{
    private string $absolutePath;
    private ?string $body;
    private array $headers;
    private string $method;

    public function __construct(string $method, string $absolutePath, ?string $body = null, array $headers = [])
    {
        $this->method = $method;
        $this->absolutePath = $absolutePath;
        $this->body = $body;
        $this->headers = $headers;
    }

    public function getAbsolutePath(): string
    {
        return $this->absolutePath;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
