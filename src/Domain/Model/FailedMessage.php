<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Domain\Model;

final class FailedMessage
{
    private ?string $messageDetails = null;
    private ?string $messageBusName = null;
    private ?string $messageOriginalTransport = null;
    private ?string $errorTrace = null;

    /** @var \DateTimeInterface[] */
    private array $failedDates = [];

    public function __construct(
        private readonly int $id,
        private readonly string $messageClass,
        private readonly string $error,
        private readonly string $errorClass,
        private readonly string $errorCode,
        private readonly \DateTimeInterface $failedAt,
    ) {
    }

    public function withMessageDetails(?string $messageDetails): self
    {
        $message = clone $this;
        $message->messageDetails = $messageDetails;

        return $message;
    }

    public function withMessageBusName(?string $messageBusName): self
    {
        $message = clone $this;
        $message->messageBusName = $messageBusName;

        return $message;
    }

    public function withMessageOriginalTransport(?string $messageOriginalTransport): self
    {
        $message = clone $this;
        $message->messageOriginalTransport = $messageOriginalTransport;

        return $message;
    }

    public function withErrorTrace(?string $errorTrace): self
    {
        $message = clone $this;
        $message->errorTrace = $errorTrace;

        return $message;
    }

    public function withFailedDates(\DateTimeInterface ...$failedDates): self
    {
        $message = clone $this;
        $message->failedDates = $failedDates;

        return $message;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMessageClass(): string
    {
        return $this->messageClass;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getErrorClass(): string
    {
        return $this->errorClass;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getFailedAt(): \DateTimeInterface
    {
        return $this->failedAt;
    }

    public function getMessageDetails(): ?string
    {
        return $this->messageDetails;
    }

    public function getMessageBusName(): ?string
    {
        return $this->messageBusName;
    }

    public function getMessageOriginalTransport(): ?string
    {
        return $this->messageOriginalTransport;
    }

    public function getErrorTrace(): ?string
    {
        return $this->errorTrace;
    }

    /**
     * @return \DateTimeInterface[]
     */
    public function getFailedDates(): array
    {
        return $this->failedDates;
    }
}
