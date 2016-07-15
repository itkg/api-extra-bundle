<?php

namespace Itkg\ApiExtraBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


/**
 * Class ItkgApiExtraExtension
 */
class ItkgApiExtraExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $this->loadConfig($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function loadConfig(ContainerBuilder $container, array $config)
    {
        if (isset($config['cache']['routes'])) {
            $container->setParameter('itkg_api_extra.routes', $config['cache']['routes']);
        }

        $container->setParameter('itkg_api_extra.tags', $config['cache']['tags']);
        $container->setParameter('itkg_api_extra.cache.adapter', $config['cache']['adapter']);
    }
}
