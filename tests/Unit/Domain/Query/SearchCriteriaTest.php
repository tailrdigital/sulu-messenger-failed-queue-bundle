<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messenger\FailedQueue;

use PHPUnit\Framework\TestCase;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\SearchCriteria;

class SearchCriteriaTest extends TestCase
{
    private SearchCriteria $criteria;

    protected function setUp(): void
    {
        $this->criteria = new SearchCriteria(
            'searchValue',
            'columnValue',
            'ASC',
            0,
            20
        );
    }

    /** @test */
    public function it_has_a_search_value(): void
    {
        self::assertSame('searchValue', $this->criteria->searchString());
    }

    /** @test */
    public function it_has_a_sort_column(): void
    {
        self::assertSame('columnValue', $this->criteria->sortColumn());
    }

    /** @test */
    public function it_has_a_sort_direction(): void
    {
        self::assertSame('ASC', $this->criteria->sortDirection());
    }

    /** @test */
    public function it_has_an_offset(): void
    {
        self::assertSame(0, $this->criteria->offset());
    }

    /** @test */
    public function it_has_a_limit(): void
    {
        self::assertSame(20, $this->criteria->limit());
    }
}
