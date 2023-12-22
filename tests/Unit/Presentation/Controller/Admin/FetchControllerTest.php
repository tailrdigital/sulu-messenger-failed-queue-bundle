<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessageInterface;
use Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin\FetchController;
use Tailr\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Query\FailedMessages;

class FetchControllerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|SerializerInterface $serializer;
    private FetchMessageInterface|ObjectProphecy $fetchMessage;
    private FetchController $controller;

    protected function setUp(): void
    {
        $this->serializer = $this->prophesize(SerializerInterface::class);
        $this->fetchMessage = $this->prophesize(FetchMessageInterface::class);
        $this->controller = new FetchController(
            $this->serializer->reveal(),
            $this->fetchMessage->reveal()
        );
    }

    /** @test */
    public function it_is_a_secured_controller(): void
    {
        self::assertInstanceOf(SecuredControllerInterface::class, $this->controller);
        self::assertSame('tailr_failed_queue', $this->controller->getSecurityContext());
    }

    /** @test */
    public function it_can_fetch_one_failed_message(): void
    {
        $this->fetchMessage->__invoke($id = 1, true)
            ->willReturn($failedMessage = FailedMessages::testFailedMessage());

        $this->serializer->serialize($failedMessage, 'json')
            ->willReturn($serializedData = '{"id": 1, "error": "Error 1"}');

        $response = ($this->controller)($id);

        self::assertSame($serializedData, $response->getContent());
        self::assertSame(200, $response->getStatusCode());
    }
}
