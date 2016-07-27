<?php

namespace Itkg\ApiExtraBundle;

use Itkg\ApiExtraBundle\DependencyInjection\Compiler\CacheAdapterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ItkgApiExtraBundle
 */
class ItkgApiExtraBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CacheAdapterCompilerPass());
    }
}
