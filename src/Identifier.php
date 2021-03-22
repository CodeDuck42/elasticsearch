<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

/**
 * @psalm-immutable
 */
final class Identifier
{
    private string $id;
    private string $index;
    private string $type;

    public function __construct(string $index, string $id, string $type = '_doc')
    {
        $this->index = $index;
        $this->id = $id;
        $this->type = $type;
    }

    public static function fromArray(array $result): self
    {
        return new self(
            isset($result['_index']) && is_string($result['_index']) ? $result['_index'] : '',
            isset($result['_id']) && is_string($result['_id']) ? $result['_id'] : '',
            isset($result['_type']) && is_string($result['_type']) ? $result['_type'] : '_doc',
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
