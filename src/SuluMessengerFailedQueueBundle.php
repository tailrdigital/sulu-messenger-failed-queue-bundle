<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tailr\SuluMessengerFailedQueueBundle\Infrastructure\Symfony\DependencyInjection\SuluMessengerFailedQueueExtension;

class SuluMessengerFailedQueueBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new SuluMessengerFailedQueueExtension();
    }
}
