<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SuluMessengerFailedQueueExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('sulu_admin')) {
            $container->prependExtensionConfig(
                'sulu_admin',
                [
                    'lists' => [
                        'directories' => [
                            __DIR__.'/../../../../config/lists',
                        ],
                    ],
                    'resources' => [
                        'tailr_messenger_failed_queue' => [
                            'routes' => [
                                'list' => 'tailr.messenger_failed_queue_list',
                                'detail' => 'tailr.messenger_failed_queue_fetch',
                            ],
                        ],
                    ],
                ]
            );
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../../../config/services'));
        $loader->load('commands.yaml');
        $loader->load('controllers.yaml');
        $loader->load('query.yaml');
        $loader->load('repositories.yaml');
        $loader->load('services.yaml');
    }
}
