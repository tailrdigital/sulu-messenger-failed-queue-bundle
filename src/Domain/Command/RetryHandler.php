<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Command;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\SingleMessageReceiver;
use Symfony\Component\Messenger\Worker;

use function Psl\Type\instance_of;

/**
 * @psalm-suppress InternalClass, InternalMethod
 */
final class RetryHandler
{
    private ?\Throwable $exception = null;

    public function __construct(
        private readonly string $transportName,
        private readonly ListableReceiverInterface $receiver,
        private readonly MessageBusInterface $messageBus,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(int $messageId): void
    {
        $message = $this->receiver->find($messageId);
        instance_of(Envelope::class)->assert($message);

        // This subscriber prevent/suppress retry strategy:
        $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(1));

        // We add a custom failed event listener to catch the exception:
        $failedListener = function (WorkerMessageFailedEvent $messageFailedEvent): void {
            $this->exception = $messageFailedEvent->getThrowable();
        };
        $this->eventDispatcher->addListener(WorkerMessageFailedEvent::class, $failedListener);

        $worker = new Worker(
            [$this->transportName => new SingleMessageReceiver($this->receiver, $message)],
            $this->messageBus,
            $this->eventDispatcher
        );

        try {
            $worker->run();
        } finally {
            $this->eventDispatcher->removeListener(WorkerMessageFailedEvent::class, $failedListener);
        }

        if ($this->exception instanceof \Throwable) {
            throw $this->exception;
        }
    }
}
