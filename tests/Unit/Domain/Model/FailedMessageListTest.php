<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageCollection;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageList;
use Tailr\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Query\FailedMessages;

class FailedMessageListTest extends TestCase
{
    private FailedMessageCollection $collection;
    private FailedMessageList $list;

    protected function setUp(): void
    {
        $this->list = new FailedMessageList(
            $this->collection = new FailedMessageCollection(
                FailedMessages::testFailedMessage(1, 'Error 1')
            ),
            1
        );
    }

    /** @test */
    public function it_has_collection(): void
    {
        self::assertSame($this->collection, $this->list->failedMessageCollection());
        self::assertCount(1, $this->list->failedMessageCollection());
    }

    /** @test */
    public function it_has_a_total_count(): void
    {
        self::assertSame(1, $this->list->totalCount());
    }
}
