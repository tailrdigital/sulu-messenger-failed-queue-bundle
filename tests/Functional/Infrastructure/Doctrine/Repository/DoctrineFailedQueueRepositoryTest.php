<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Tests\Functional\Infrastructure\Doctrine\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection as DoctrineTransportConnection;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\SearchCriteria;
use Tailr\SuluMessengerFailedQueueBundle\Infrastructure\Doctrine\Repository\DoctrineFailedQueueRepository;

use function Psl\Result\wrap;

class DoctrineFailedQueueRepositoryTest extends TestCase
{
    private Connection $connection;
    private EntityManager $entityManager;
    private DoctrineFailedQueueRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = DriverManager::getConnection([
            'url' => $_ENV['DATABASE_URL'],
        ]);
        $this->entityManager = new EntityManager(
            $this->connection,
            ORMSetup::createAttributeMetadataConfiguration([], true)
        );
        $this->repository = new DoctrineFailedQueueRepository($this->entityManager);
        $this->cleanup();
        $this->createFixtures($this->entityManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanup();
    }

    private function cleanup(): void
    {
        wrap(fn () => $this->connection->executeQuery('TRUNCATE messenger_messages'));
    }

    /** @test */
    public function it_can_find_message_ids(): void
    {
        $criteria = new SearchCriteria('', 'created_at', 'ASC', 0, 100);
        $ids = $this->repository->findMessageIds($criteria);
        $totalCount = $this->repository->count($criteria);

        self::assertCount(3, $ids);
        self::assertSame(3, $totalCount);
    }

    /** @test */
    public function it_can_find_message_ids_by_search_string(): void
    {
        $criteria = new SearchCriteria('message1', 'created_at', 'ASC', 0, 100);
        $ids = $this->repository->findMessageIds($criteria);
        $totalCount = $this->repository->count($criteria);

        self::assertCount(1, $ids);
        self::assertSame(1, $totalCount);
    }

    /** @test */
    public function it_can_find_message_ids_ordered_descending(): void
    {
        $criteria = new SearchCriteria('', 'created_at', 'DESC', 0, 100);

        $ids = $this->repository->findMessageIds($criteria);
        $totalCount = $this->repository->count($criteria);

        self::assertCount(3, $ids);
        self::assertSame(3, $totalCount);
        self::assertGreaterThan($ids[0], $ids[2]);
    }

    /** @test */
    public function it_can_find_message_ids_ordered_by_default(): void
    {
        $criteria = new SearchCriteria('', null, null, 0, 100);

        $ids = $this->repository->findMessageIds($criteria);
        $totalCount = $this->repository->count($criteria);

        self::assertCount(3, $ids);
        self::assertSame(3, $totalCount);
        self::assertGreaterThan($ids[0], $ids[2]);
    }

    protected function createFixtures(EntityManagerInterface $entityManager): void
    {
        $doctrineTransportConnection = new DoctrineTransportConnection([
            'table_name' => 'messenger_messages',
            'queue_name' => 'failed',
        ], $this->connection);
        $doctrineTransportConnection->setup();

        $doctrineTransportConnection->send('serialized message1', []);
        $doctrineTransportConnection->send('serialized message2', []);
        $doctrineTransportConnection->send('serialized message3', []);
    }
}
