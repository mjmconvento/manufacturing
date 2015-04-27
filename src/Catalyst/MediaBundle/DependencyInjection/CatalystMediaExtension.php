<?php

namespace Catalyst\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Catalyst\MediaBundle\Model\StorageEngine;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

class CatalystMediaExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        // storage config
        $container->setParameter('catalyst_media.storage_config', $config[0]['upload_storage']);

        // load services.yml
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
