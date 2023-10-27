<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Command\DeleteHandler;

#[Route(path: '/messenger-failed-queue/{id}', name: 'app.messenger_failed_queue_delete', methods: ['DELETE'])]
final class DeleteController extends AbstractSecuredMessengerFailedQueueController implements SecuredControllerInterface
{
    public function __construct(
        private readonly DeleteHandler $handler,
    ) {
    }

    public function __invoke(int $id, Request $request): Response
    {
        ($this->handler)($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
