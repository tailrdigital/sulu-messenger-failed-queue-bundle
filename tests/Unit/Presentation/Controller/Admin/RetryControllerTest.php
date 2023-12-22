<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Command\RetryHandlerInterface;
use Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin\RetryController;

class RetryControllerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|RetryHandlerInterface $handler;
    private RetryController $controller;

    protected function setUp(): void
    {
        $this->handler = $this->prophesize(RetryHandlerInterface::class);
        $this->controller = new RetryController(
            $this->handler->reveal()
        );
    }

    /** @test */
    public function it_is_a_secured_controller(): void
    {
        self::assertInstanceOf(SecuredControllerInterface::class, $this->controller);
        self::assertSame('tailr_failed_queue', $this->controller->getSecurityContext());
        self::assertSame('en', $this->controller->getLocale(new Request()));
    }

    /** @test */
    public function it_can_retry_failed_messages(): void
    {
        $request = new Request(content: json_encode([
            'identifiers' => [1, 2],
        ]));

        $this->handler->__invoke(1)->shouldBeCalledOnce();
        $this->handler->__invoke(2)->shouldBeCalledOnce();

        $response = ($this->controller)($request);

        self::assertSame(204, $response->getStatusCode());
    }

    /** @test */
    public function it_will_respond_with_bad_request_when_retry_failed(): void
    {
        $request = new Request(content: json_encode([
            'identifiers' => [1, 2],
        ]));

        $this->handler->__invoke(1)->willThrow(new \RuntimeException('Something failed'));

        $response = ($this->controller)($request);

        self::assertSame(400, $response->getStatusCode());
    }
}
