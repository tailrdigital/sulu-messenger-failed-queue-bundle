<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Command;

interface RequeueHandlerInterface
{
    public function __invoke(int $messageId): void;
}
