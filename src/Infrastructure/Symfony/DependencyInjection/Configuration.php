<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress PossiblyNullReference, UndefinedInterfaceMethod, MixedMethodCall
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sulu_messenger_failed_queue');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('failure_transport_service')
            ->defaultValue('messenger.transport.failed')
            ->end()
            ->scalarNode('failure_transport_table')
            ->defaultValue('messenger_messages')
            ->end()
            ->scalarNode('failure_transport_queue_name')
            ->defaultValue('failed')
            ->end();

        return $treeBuilder;
    }
}
