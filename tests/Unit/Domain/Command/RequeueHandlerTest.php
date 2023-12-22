<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psl\Type\Exception\AssertException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Command\RequeueHandler;

class RequeueHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|ListableReceiverInterface $receiver;
    private ObjectProphecy|RoutableMessageBus $messageBus;
    private RequeueHandler $handler;

    protected function setUp(): void
    {
        $this->receiver = $this->prophesize(ListableReceiverInterface::class);
        $this->messageBus = $this->prophesize(RoutableMessageBus::class);
        $this->handler = new RequeueHandler(
            $this->receiver->reveal(),
            $this->messageBus->reveal(),
            [
                ErrorDetailsStamp::class,
            ]
        );
    }

    /** @test */
    public function it_can_requeue_a_failed_message(): void
    {
        $envelope = new Envelope(
            $message = new \stdClass(),
            [
                $busStamp = new BusNameStamp($busName = 'default_bus'),
                new ErrorDetailsStamp(\Exception::class, '0', 'Error'),
            ]
        );
        $this->receiver->find(1)->willReturn($envelope);
        $defaultBus = $this->prophesize(MessageBusInterface::class);
        $this->messageBus->getMessageBus($busName)->willReturn($defaultBus);
        $defaultBus->dispatch($message, [$busStamp])->willReturn(new Envelope(new \stdClass(), []));
        $this->receiver->reject($envelope)->shouldBeCalledOnce();

        ($this->handler)(1);
    }

    /** @test */
    public function it_throws_exception_when_bus_name_is_unknown(): void
    {
        $envelope = new Envelope(new \stdClass(), []);
        $this->receiver->find(1)->willReturn($envelope);
        $this->messageBus->getMessageBus(Argument::any())->shouldNotBeCalled($envelope);
        $this->messageBus->dispatch(Argument::any())->shouldNotBeCalled($envelope);
        $this->receiver->reject($envelope)->shouldNotBeCalled();

        self::expectException(AssertException::class);
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
