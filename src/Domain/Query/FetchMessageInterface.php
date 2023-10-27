<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Query;

use Tailr\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessage;

interface FetchMessageInterface
{
    public function __invoke(int $messageId, bool $withDetails): FailedMessage;
}
