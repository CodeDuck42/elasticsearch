<?php

declare(strict_types=1);

namespace CodeDuck\Elasticsearch;

use CodeDuck\Elasticsearch\Actions\Bulk;
use CodeDuck\Elasticsearch\Actions\Delete;
use CodeDuck\Elasticsearch\Actions\Index;
use CodeDuck\Elasticsearch\Actions\Query;
use CodeDuck\Elasticsearch\Contracts\ClientInterface;
use CodeDuck\Elasticsearch\Exceptions\TransportException;
use CodeDuck\Elasticsearch\ValueObjects\QueryResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

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

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerBulkDeleteWithNoneExistingDocument(): void
    {
        $sut = new SimpleClient(new Client(HttpClient::create(), 'http://localhost:9200'), 'test-index');
        $sut->begin();
        $sut->delete('TBDWNED');
        $sut->commit();

        self::assertTrue(true);
    }

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerDeleteWithExistingDocument(): void
    {
        $sut = new SimpleClient(new Client(HttpClient::create(), 'http://localhost:9200'), 'test-index');
        $sut->add('TDWED', ['name' => 'example']);
        sleep(5); // wait for index
        $sut->delete('TDWED');

        self::assertTrue(true);
    }

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerDeleteWithNoneExistingDocument(): void
    {
        $this->expectException(TransportException::class);

        $sut = new SimpleClient(new Client(HttpClient::create(), 'http://localhost:9200'), 'test-index');
        $sut->delete('TDWNED');
    }

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerIndex(): void
    {
        $sut = new SimpleClient(new Client(HttpClient::create(), 'http://localhost:9200'), 'test-index');
        $sut->add('11111', ['name' => 'example']);

        self::assertTrue(true);
    }

    /**
     * @group integration
     */
    public function testOnTheIntegrationServerQuery(): void
    {
        $sut = new SimpleClient(new Client(HttpClient::create(), 'http://localhost:9200'), 'test-index');
        $sut->begin();
        $sut->add('11111', ['name' => 'example']);
        $sut->add('22222', ['name' => 'banana']);
        $sut->commit();

        sleep(5); // wait for index

        $result = $sut->query(['query' => ['term' => ['name' => 'banana']]]);

        self::assertEquals(1, $result->getCount());
        self::assertEquals(['name' => 'banana'], $result->getDocuments()[0]->getSource());
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
