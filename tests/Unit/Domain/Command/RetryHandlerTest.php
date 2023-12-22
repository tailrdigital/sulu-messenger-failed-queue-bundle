<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psl\Type\Exception\AssertException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Command\RetryHandler;

class RetryHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|ListableReceiverInterface $receiver;
    private ObjectProphecy|MessageBusInterface $messageBus;
    private ObjectProphecy|EventDispatcherInterface $eventDispatcher;
    private RetryHandler $handler;

    protected function setUp(): void
    {
        $this->receiver = $this->prophesize(ListableReceiverInterface::class);
        $this->messageBus = $this->prophesize(MessageBusInterface::class);
        $this->eventDispatcher = new EventDispatcher();
        $this->handler = new RetryHandler(
            'failed',
            $this->receiver->reveal(),
            $this->messageBus->reveal(),
            $this->eventDispatcher,
        );
    }

    /** @test */
    public function it_can_retry_a_failed_message(): void
    {
        $envelope = new Envelope(new \stdClass(), []);
        $this->receiver->find(1)->willReturn($envelope);
        $this->receiver->ack($envelope)->shouldBeCalledOnce();
        $this->receiver->reject($envelope)->shouldNotBeCalled();

        $this->messageBus->dispatch(Argument::type(Envelope::class))->willReturn($envelope);

        ($this->handler)(1);
    }

    /** @test */
    public function it_throws_exception_when_retry_failed(): void
    {
        $envelope = new Envelope(new \stdClass(), []);
        $this->receiver->find(1)->willReturn($envelope);
        $this->receiver->ack($envelope)->shouldNotBeCalled();
        $this->receiver->reject($envelope)->shouldBeCalledOnce();

        $this->messageBus->dispatch(Argument::type(Envelope::class))->willThrow($exception = new \Exception('Some error'));

        self::expectExceptionMessage($exception->getMessage());
        self::expectException(\Exception::class);
        ($this->handler)(1);
    }

    /** @test */
    public function it_throws_exception_when_not_found(): void
    {
        $this->receiver->find(1)->willReturn(null);
        $this->receiver->ack(Argument::any())->shouldNotBeCalled();

        self::expectException(AssertException::class);
        ($this->handler)(1);
    }
}
