<?php

namespace Itkg\ApiExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * class CacheAdapterCompilerPass 
 */
class CacheAdapterCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->setAlias('itkg_api_extra.cache.adapter', $container->getParameter('itkg_api_extra.cache.adapter'));
    }
}
