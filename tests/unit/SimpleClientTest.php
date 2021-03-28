<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Actions\Bulk;
use CodeDuck\Elasticsearch\Actions\Delete;
use CodeDuck\Elasticsearch\Actions\Index;
use CodeDuck\Elasticsearch\Actions\Query;
use CodeDuck\Elasticsearch\Contracts\ClientInterface;
use CodeDuck\Elasticsearch\ValueObjects\QueryResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CodeDuck\Elasticsearch\SimpleClient
 */
class SimpleClientTest extends TestCase
{
    /** @var ClientInterface & MockObject */
    private $clientMock;
    private SimpleClient $sut;

    public function testAddingDocuments(): void
    {
        $this->clientMock
            ->expects(self::once())
            ->method('execute')
            ->with(self::isInstanceOf(Index::class));

        $this->sut->add('1234', ['foo' => 'bar']);
    }

    public function testCommittingAgainShouldNotBeProcessed(): void
    {
        $this->clientMock
            ->expects(self::once())
            ->method('execute');

        $this->sut->begin();
        $this->sut->add('1234', ['foo' => 'bar']);
        $this->sut->commit();
        $this->sut->commit();
    }

    public function testCommittingBulkActions(): void
    {
        $this->clientMock
            ->expects(self::once())
            ->method('execute')
            ->with(self::isInstanceOf(Bulk::class));

        $this->sut->begin();
        $this->sut->add('1234', ['foo' => 'bar']);
        $this->sut->delete('1234');
        $this->sut->commit();
    }

    public function testCommittingShouldResetTheBulkStatus(): void
    {
        $this->clientMock
            ->expects(self::exactly(2))
            ->method('execute')
            ->withConsecutive(
                [self::isInstanceOf(Bulk::class)],
                [self::isInstanceOf(Delete::class)]
            );

        $this->sut->begin();
        $this->sut->delete('1234');
        $this->sut->commit();

        $this->sut->delete('1234');
    }

    public function testCommittingShouldResetTheBulkStatusEmptyBulkActionsShouldNotBeProcessed(): void
    {
        $this->clientMock
            ->expects(self::never())
            ->method('execute');

        $this->sut->begin();
        $this->sut->commit();
    }

    public function testDeletingDocuments(): void
    {
        $this->clientMock
            ->expects(self::once())
            ->method('execute')
            ->with(self::isInstanceOf(Delete::class));

        $this->sut->delete('1234');
    }

    public function testQuery(): void
    {
        $this->clientMock
            ->expects(self::once())
            ->method('execute')
            ->with(self::isInstanceOf(Query::class))
            ->willReturn(new QueryResult([], 123, 0.9));

        $this->sut->query([]);
    }

    public function testQueryDuringOpenBulkShouldBeDirectlyProcessed(): void
    {
        $this->clientMock
            ->expects(self::once())
            ->method('execute')
            ->with(self::isInstanceOf(Query::class))
            ->willReturn(new QueryResult([], 123, 0.9));

        $this->sut->begin();
        $this->sut->query([]);
        $this->sut->commit();
    }

    public function testRollBack(): void
    {
        $this->clientMock
            ->expects(self::never())
            ->method('execute')
            ->with(self::isInstanceOf(Bulk::class));

        $this->sut->begin();
        $this->sut->add('1234', ['foo' => 'bar']);
        $this->sut->rollBack();

        // nothing should happen here
        $this->sut->begin();
        $this->sut->commit();
    }

    public function testRollBackShouldResetTheBulkStatus(): void
    {
        $this->clientMock
            ->expects(self::once())
            ->method('execute')
            ->with(self::isInstanceOf(Delete::class));

        $this->sut->begin();
        $this->sut->add('1234', ['foo' => 'bar']);
        $this->sut->rollBack();

        $this->sut->delete('1234');
    }

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->sut = new SimpleClient($this->clientMock, 'test-index', 'document');
    }
}
