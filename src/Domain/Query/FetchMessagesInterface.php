<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Query;

use Tailr\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageList;

interface FetchMessagesInterface
{
    public function __invoke(SearchCriteria $criteria): FailedMessageList;
}
