<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessage;

class FailedMessageTest extends TestCase
{
    private FailedMessage $failedMessage;

    protected function setUp(): void
    {
        $this->failedMessage = new FailedMessage(
            1,
            \stdClass::class,
            'Error',
            \Exception::class,
            '500',
            new \DateTimeImmutable('2023-01-01 00:00:00')
        );
    }

    /** @test */
    public function it_has_an_id(): void
    {
        self::assertSame(1, $this->failedMessage->getId());
    }

    /** @test */
    public function it_has_a_message_class(): void
    {
        self::assertSame(\stdClass::class, $this->failedMessage->getMessageClass());
    }

    /** @test */
    public function it_has_an_error(): void
    {
        self::assertSame('Error', $this->failedMessage->getError());
    }

    /** @test */
    public function it_has_an_error_class(): void
    {
        self::assertSame(\Exception::class, $this->failedMessage->getErrorClass());
    }

    /** @test */
    public function it_has_an_error_code(): void
    {
        self::assertSame('500', $this->failedMessage->getErrorCode());
    }

    /** @test */
    public function it_has_a_failed_at(): void
    {
        self::assertEquals(new \DateTimeImmutable('2023-01-01 00:00:00'), $this->failedMessage->getFailedAt());
    }

    /** @test */
    public function it_could_have_a_message_details(): void
    {
        $failedMessage = $this->failedMessage->withMessageDetails($value = '[details]');
        self::assertSame($value, $failedMessage->getMessageDetails());
    }

    /** @test */
    public function it_could_have_a_message_original_transport(): void
    {
        $failedMessage = $this->failedMessage->withMessageOriginalTransport($value = 'async');
        self::assertSame($value, $failedMessage->getMessageOriginalTransport());
    }

    /** @test */
    public function it_could_have_a_message_bus_name(): void
    {
        $failedMessage = $this->failedMessage->withMessageBusName($value = 'default_bus');
        self::assertSame($value, $failedMessage->getMessageBusName());
    }

    /** @test */
    public function it_could_have_an_error_trace(): void
    {
        $failedMessage = $this->failedMessage->withErrorTrace($value = 'exception at:0');
        self::assertSame($value, $failedMessage->getErrorTrace());
    }

    /** @test */
    public function it_could_have_failed_dates(): void
    {
        $failedMessage = $this->failedMessage->withFailedDates(
            $date1 = new \DateTimeImmutable('2023-01-01 00:00:00'),
            $date2 = new \DateTimeImmutable('2023-01-02 00:00:00'),
        );
        self::assertSame(
            [$date1, $date2],
            $failedMessage->getFailedDates()
        );
    }
}
