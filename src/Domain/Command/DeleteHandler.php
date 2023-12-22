<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Command;

use Symfony\Component\Messenger\Envelope;

use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

use function Psl\Type\instance_of;

final class DeleteHandler implements DeleteHandlerInterface
{
    public function __construct(
        private readonly ListableReceiverInterface $receiver,
    ) {
    }

    public function __invoke(int $messageId): void
    {
        $message = $this->receiver->find($messageId);
        instance_of(Envelope::class)->assert($message);

        $this->receiver->reject($message);
    }
}
