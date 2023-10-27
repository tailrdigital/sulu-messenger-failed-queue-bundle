<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Command\RequeueHandler;

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
