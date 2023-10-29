<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psl\Type\Exception\AssertException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Command\DeleteHandler;

class DeleteHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|ListableReceiverInterface $receiver;
    private DeleteHandler $handler;

    protected function setUp(): void
    {
        $this->receiver = $this->prophesize(ListableReceiverInterface::class);
        $this->handler = new DeleteHandler(
            $this->receiver->reveal()
        );
    }

    /** @test */
    public function it_can_delete_a_failed_message(): void
    {
        $envelope = new Envelope(new \stdClass(), []);
        $this->receiver->find(1)->willReturn($envelope);
        $this->receiver->reject($envelope)->shouldBeCalledOnce();

        ($this->handler)(1);
    }

    /** @test */
    public function it_throws_exception_when_not_found(): void
    {
        $this->receiver->find(1)->willReturn(null);
        $this->receiver->reject(Argument::any())->shouldNotBeCalled();

        self::expectException(AssertException::class);
        ($this->handler)(1);
    }
}
