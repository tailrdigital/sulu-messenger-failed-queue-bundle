<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Command\DeleteHandlerInterface;
use Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin\DeleteController;

class DeleteControllerTest extends TestCase
{
    use ProphecyTrait;

    private DeleteHandlerInterface|ObjectProphecy $deleteHandler;
    private DeleteController $controller;

    protected function setUp(): void
    {
        $this->deleteHandler = $this->prophesize(DeleteHandlerInterface::class);
        $this->controller = new DeleteController(
            $this->deleteHandler->reveal()
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
    public function it_can_handle_delete_request_for_removing_failed_messages(): void
    {
        $this->deleteHandler->__invoke($id = 1)->shouldBeCalledOnce();
        $response = ($this->controller)($id);

        self::assertSame(204, $response->getStatusCode());
    }
}
