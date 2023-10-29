<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messenger\FailedQueue;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psl\Type\Exception\AssertException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessage;

class FetchMessageTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|ListableReceiverInterface $receiver;
    private ObjectProphecy|SerializerInterface $serializer;
    private FetchMessage $query;

    protected function setUp(): void
    {
        $this->receiver = $this->prophesize(ListableReceiverInterface::class);
        $this->serializer = $this->prophesize(SerializerInterface::class);
        $this->query = new FetchMessage(
            $this->receiver->reveal(),
            $this->serializer->reveal(),
        );
    }

    /** @test */
    public function it_can_fetch_a_failed_message_without_details(): void
    {
        $now = new \DateTimeImmutable('now');
        $error = new \Exception('Some error', 111);
        $envelope = new Envelope(new \stdClass(), [
            new TransportMessageIdStamp($id = 1),
            ErrorDetailsStamp::create($error),
            new RedeliveryStamp(1, $now),

        ]);

        $this->receiver->find(1)->willReturn($envelope);

        $failedMessage = ($this->query)(1, withDetails: false);
        self::assertSame($id, $failedMessage->getId());
        self::assertSame($error->getMessage(), $failedMessage->getError());
        self::assertSame($error::class, $failedMessage->getErrorClass());
        self::assertSame('111', $failedMessage->getErrorCode());
        self::assertSame($now, $failedMessage->getFailedAt());
    }

    /** @test */
    public function it_can_fetch_a_failed_message_without_details_an_missing_error_information(): void
    {
        $now = new \DateTimeImmutable('now');
        $envelope = new Envelope(new \stdClass(), [
            new TransportMessageIdStamp($id = 1),
            new RedeliveryStamp(1, $now),
        ]);

        $this->receiver->find(1)->willReturn($envelope);

        $failedMessage = ($this->query)(1, withDetails: false);
        self::assertSame($id, $failedMessage->getId());
        self::assertSame('Unknown error', $failedMessage->getError());
        self::assertSame('Unknown error class', $failedMessage->getErrorClass());
        self::assertSame('0', $failedMessage->getErrorCode());
        self::assertSame($now, $failedMessage->getFailedAt());
    }

    /** @test */
    public function it_can_fetch_a_failed_message_with_details(): void
    {
        $lastHour = new \DateTimeImmutable('now -1 hour');
        $now = new \DateTimeImmutable('now');
        $error = new \Exception('Some error', 111);
        $envelope = new Envelope($message = new \stdClass(), [
            new TransportMessageIdStamp($id = 1),
            ErrorDetailsStamp::create($error),
            new RedeliveryStamp(1, $lastHour),
            new RedeliveryStamp(1, $now),
            new BusNameStamp($busName = 'default_bus'),
            new SentToFailureTransportStamp($originalTransport = 'async'),
        ]);

        $this->receiver->find(1)->willReturn($envelope);
        $this->serializer->serialize($message, 'json')->willReturn($messageDetails = '{stdClass}');

        $failedMessage = ($this->query)(1, withDetails: true);
        self::assertSame($id, $failedMessage->getId());
        self::assertSame($error->getMessage(), $failedMessage->getError());
        self::assertSame($error::class, $failedMessage->getErrorClass());
        self::assertSame('111', $failedMessage->getErrorCode());
        self::assertSame($now, $failedMessage->getFailedAt());
        self::assertSame($messageDetails, $failedMessage->getMessageDetails());
        self::assertSame($busName, $failedMessage->getMessageBusName());
        self::assertSame($originalTransport, $failedMessage->getMessageOriginalTransport());
        self::assertSame([$lastHour, $now], $failedMessage->getFailedDates());
    }

    /** @test */
    public function it_throws_exception_when_not_found(): void
    {
        $this->receiver->find(1)->willReturn(null);

        self::expectException(AssertException::class);
        ($this->query)(1, withDetails: true);
    }
}
