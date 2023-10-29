<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Query;

use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;

use Tailr\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageCollection;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageList;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Repository\FailedQueueRepositoryInterface;

final class FetchMessages
{
    public function __construct(
        private readonly FailedQueueRepositoryInterface $repository,
        private readonly FetchMessageInterface $fetchMessage,
    ) {
    }

    public function __invoke(SearchCriteria $criteria): FailedMessageList
    {
        $messages = [];
        foreach ($this->repository->findMessageIds($criteria) as $messageId) {
            try {
                $messages[] = ($this->fetchMessage)($messageId, withDetails: false);
            } catch (MessageDecodingFailedException) {
            }
        }

        return new FailedMessageList(
            new FailedMessageCollection(...$messages),
            $this->repository->count($criteria),
        );
    }
}
