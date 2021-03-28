<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\ValueObjects;

/**
 * @psalm-immutable
 */
final class Document
{
    private Identifier $identifier;
    private float $score;
    private array $source;

    public function __construct(Identifier $identifier, array $source, float $score = 0.0)
    {
        $this->identifier = $identifier;
        $this->source = $source;
        $this->score = $score;
    }

    public static function fromArray(array $result): self
    {
        return new self(
            Identifier::fromArray($result),
            isset($result['_source']) && is_array($result['_source']) ? $result['_source'] : [],
            isset($result['_score']) && is_float($result['_score']) ? $result['_score'] : 0.0
        );
    }

    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getSource(): array
    {
        return $this->source;
    }
}
