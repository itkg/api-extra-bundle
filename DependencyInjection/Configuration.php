<?php

namespace Itkg\ApiExtraBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * class Configuration 
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('itkg_api_extra');
        $rootNode
            ->children()
                ->arrayNode('cache')
                    ->children()
                        ->arrayNode('tags')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->arrayNode('associated')
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->enumNode('decache_type')
                                        ->values(['strict', 'wildcard'])
                                        ->defaultValue('strict')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('routes')
                            ->useAttributeAsKey('route')
                            ->prototype('array')
                            ->children()
                                ->scalarNode('route')->end()
                                ->integerNode('duration')->end()
                                ->arrayNode('tags')->
                                    prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
