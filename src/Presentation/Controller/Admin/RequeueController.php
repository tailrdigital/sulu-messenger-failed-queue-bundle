<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Command\RequeueHandler;

#[Route(path: '/messenger-failed-queue/{id}/requeue', name: 'app.messenger_failed_queue_requeue', methods: ['PUT'])]
final class RequeueController extends AbstractSecuredMessengerFailedQueueController implements SecuredControllerInterface
{
    public function __construct(
        private readonly RequeueHandler $handler,
    ) {
    }

    public function __invoke(int $id, Request $request): Response
    {
        try {
            ($this->handler)($id);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return new JsonResponse(['detail' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
