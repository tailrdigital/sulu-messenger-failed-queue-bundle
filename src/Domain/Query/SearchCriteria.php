<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Query;

final class SearchCriteria
{
    public function __construct(
        private readonly string $searchString,
        private readonly ?string $sortColumn,
        private readonly ?string $sortDirection,
        private readonly int $offset,
        private readonly int $limit
    ) {
    }

    public function searchString(): string
    {
        return $this->searchString;
    }

    public function sortColumn(): ?string
    {
        return $this->sortColumn;
    }

    public function sortDirection(): ?string
    {
        return $this->sortDirection;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function limit(): int
    {
        return $this->limit;
    }
}
