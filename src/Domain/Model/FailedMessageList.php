<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Model;

final class FailedMessageList
{
    public function __construct(
        private readonly FailedMessageCollection $failedMessageCollection,
        private readonly int $totalCount,
    ) {
    }

    public function failedMessageCollection(): FailedMessageCollection
    {
        return $this->failedMessageCollection;
    }

    public function totalCount(): int
    {
        return $this->totalCount;
    }
}
