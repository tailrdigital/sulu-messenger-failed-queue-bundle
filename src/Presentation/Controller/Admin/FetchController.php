<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessageInterface;

#[Route(path: '/messenger-failed-queue/{id}', name: 'tailr.messenger_failed_queue_fetch', options: ['expose' => true], methods: ['GET'])]
final class FetchController extends AbstractSecuredMessengerFailedQueueController implements SecuredControllerInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly FetchMessageInterface $fetchMessage,
    ) {
    }

    public function __invoke(int $id): Response
    {
        return new JsonResponse(
            $this->serializer->serialize(
                ($this->fetchMessage)($id, withDetails: true),
                'json',
            ),
            json: true
        );
    }
}
