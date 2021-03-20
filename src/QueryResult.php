<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

/**
 * @psalm-immutable
 */
final class QueryResult
{
    /** @var Document[] */
    private array $documents = [];
    private int $took;
    private float $maxScore;

    /**
     * @param Document[] $documents
     */
    public function __construct(array $documents, int $took, float $maxScore)
    {
        $this->documents = $documents;
        $this->took = $took;
        $this->maxScore = $maxScore;
    }

    public static function fromArray(array $result): self
    {
        $documents = [];

        /** @psalm-suppress MixedAssignment */
        if (isset($result['hits']['hits']) && is_array($result['hits']['hits'])) {
            foreach ($result['hits']['hits'] as $documentArray) {
                /** @psalm-suppress MixedArgument */
                $documents[] = Document::fromArray($documentArray);
            }
        }

        return new self(
            $documents,
            isset($result['took']) && is_int($result['took']) ? $result['took'] : 0,
            isset($result['hits']['max_score']) && is_float($result['hits']['max_score'])
                ? $result['hits']['max_score']
                : 0.0
        );
    }

    /**
     * @return Document[]
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function getCount(): int
    {
        return count($this->documents);
    }

    public function getTook(): int
    {
        return $this->took;
    }

    public function getMaxScore(): float
    {
        return $this->maxScore;
    }
}
