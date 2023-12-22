<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messenger\FailedQueue;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessageInterface;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessages;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\SearchCriteria;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Repository\FailedQueueRepositoryInterface;
use Tailr\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Query\FailedMessages;

class FetchMessagesTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|FetchMessageInterface $fetchAction;
    private ObjectProphecy|FailedQueueRepositoryInterface $repository;
    private FetchMessages $fetchMessages;

    protected function setUp(): void
    {
        $this->fetchAction = $this->prophesize(FetchMessageInterface::class);
        $this->repository = $this->prophesize(FailedQueueRepositoryInterface::class);
        $this->fetchMessages = new FetchMessages(
            $this->repository->reveal(),
            $this->fetchAction->reveal(),
        );
    }

    /** @test */
    public function it_can_retrieve_a_list_of_failed_messages(): void
    {
        $listSearch = new SearchCriteria('foo', 'id', 'ASC', 10, 10);

        $messageOne = FailedMessages::testFailedMessage(1);
        $messageTwo = FailedMessages::testFailedMessage(2);
        $messageThree = FailedMessages::testFailedMessage(3);
        $this->repository->findMessageIds(Argument::is($listSearch))->willReturn([1, 2, 3]);
        $this->fetchAction->__invoke(1, false)->willReturn($messageOne);
        $this->fetchAction->__invoke(2, false)->willReturn($messageTwo);
        $this->fetchAction->__invoke(3, false)->willThrow(MessageDecodingFailedException::class);

        $this->repository->count(Argument::is($listSearch))->willReturn(20);

        $messageList = ($this->fetchMessages)($listSearch);
        $messages = iterator_to_array($messageList->failedMessageCollection());
        self::assertEquals($messageOne, $messages[0]);
        self::assertEquals($messageTwo, $messages[1]);
        self::assertSame(2, $messageList->failedMessageCollection()->count());
        self::assertSame(20, $messageList->totalCount());
    }
}
