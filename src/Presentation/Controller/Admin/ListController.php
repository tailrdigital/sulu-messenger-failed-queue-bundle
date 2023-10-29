<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Sulu\Component\Rest\ListBuilder\ListRestHelperInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessages;
use Tailr\SuluMessengerFailedQueueBundle\Domain\Query\SearchCriteria;

use function Psl\Str\is_empty;
use function Psl\Type\int;

#[Route(path: '/messenger-failed-queue', name: 'tailr.messenger_failed_queue_list', options: ['expose' => true], methods: ['GET'])]
final class ListController extends AbstractSecuredMessengerFailedQueueController implements SecuredControllerInterface
{
    public const RESOURCE_KEY = 'tailr_messenger_failed_queue';

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ListRestHelperInterface $listRestHelper,
        private readonly FetchMessages $fetchMessages,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $sortColumn = $this->listRestHelper->getSortColumn();
        $limit = int()->coerce($this->listRestHelper->getLimit());
        $list = ($this->fetchMessages)(
            new SearchCriteria(
                (string) $this->listRestHelper->getSearchPattern(),
                is_empty($sortColumn) ? 'created_at' : $sortColumn,
                is_empty($sortColumn) ? 'DESC' : $this->listRestHelper->getSortOrder(),
                (int) $this->listRestHelper->getOffset(),
                $limit
            )
        );

        $listRepresentation = new PaginatedRepresentation(
            $list->failedMessageCollection(),
            self::RESOURCE_KEY,
            (int) $this->listRestHelper->getPage(),
            $limit,
            $list->totalCount()
        );

        return new JsonResponse(
            $this->serializer->serialize($listRepresentation->toArray(), 'json'),
            json: true
        );
    }
}
