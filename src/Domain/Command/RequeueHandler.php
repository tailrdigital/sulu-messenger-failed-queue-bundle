<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Command;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\RoutableMessageBus;

use Symfony\Component\Messenger\Stamp\BusNameStamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

use function Psl\Type\instance_of;
use function Psl\Type\string;
use function Psl\Type\vec;

/**
 * @psalm-suppress InternalMethod
 */
final class RequeueHandler implements RequeueHandlerInterface
{
    /**
     * @var class-string[]
     */
    private array $stampsToRemove;

    public function __construct(
        private readonly ListableReceiverInterface $receiver,
        private readonly RoutableMessageBus $messageBus,
        array $stampsToRemove,
    ) {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->stampsToRemove = vec(string())->coerce($stampsToRemove);
    }

    public function __invoke(int $messageId): void
    {
        $message = $this->receiver->find($messageId);
        instance_of(Envelope::class)->assert($message);
        $busStamp = $message->last(BusNameStamp::class);
        instance_of(BusNameStamp::class)->assert($busStamp);

        $bus = $this->messageBus->getMessageBus($busStamp->getBusName());
        $bus->dispatch($message->getMessage(), $this->calculateRequeueStamps($message));

        // Reject/remove original message
        $this->receiver->reject($message);
    }

    /**
     * @return StampInterface[]
     */
    private function calculateRequeueStamps(Envelope $envelope): array
    {
        $tmpEnvelope = clone $envelope;
        foreach ($this->stampsToRemove as $stampFqcn) {
            $tmpEnvelope = $tmpEnvelope->withoutAll($stampFqcn);
        }

        return array_merge(...array_values($tmpEnvelope->all()));
    }
}
