<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\SerializerInterface;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessageInterface;

final class FetchController extends AbstractSecuredMessengerFailedQueueController implements SecuredControllerInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly FetchMessageInterface $query,
    ) {
    }

    public function __invoke(int $id, Request $request): Response
    {
        return new JsonResponse(
            $this->serializer->serialize(
                ($this->query)($id, withDetails: true),
                'json',
            ),
            json: true
        );
    }
}
