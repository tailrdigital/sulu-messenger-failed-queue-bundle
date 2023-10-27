<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Model;

use IteratorAggregate;

use function Psl\Iter\first;

/**
 * @psalm-immutable
 *
 * @template-implements IteratorAggregate<int, FailedMessage>
 */
final class FailedMessageCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var FailedMessage[]
     */
    private array $messages;

    /**
     * @no-named-arguments
     */
    public function __construct(FailedMessage ...$messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return \Traversable<int, FailedMessage>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->messages;
    }

    public function count(): int
    {
        return count($this->messages);
    }

    public function first(): ?FailedMessage
    {
        /** @psalm-suppress ImpureFunctionCall */
        return first($this->messages);
    }
}
