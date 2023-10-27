<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Tailr\SuluMessengerFailedQueueBundle\Infrastructure\Sulu\Admin\MessengerFailedQueueAdmin;

abstract class AbstractSecuredMessengerFailedQueueController implements SecuredControllerInterface
{
    public function getSecurityContext(): string
    {
        return MessengerFailedQueueAdmin::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): string
    {
        return $request->getLocale();
    }
}
