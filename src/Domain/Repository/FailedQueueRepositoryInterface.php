<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Repository;

use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\SearchCriteria;

interface FailedQueueRepositoryInterface
{
    /**
     * @return int[]
     */
    public function findMessageIds(SearchCriteria $criteria): array;

    public function count(SearchCriteria $criteria): int;
}
