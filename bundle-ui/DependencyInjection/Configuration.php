<?php

namespace Netgen\Bundle\InformationCollectionPlatformUIBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const CONFIGURATION_ROOT = 'netgen_information_collection_platform_ui';

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder();
        $rootNode = $tree->root(self::CONFIGURATION_ROOT);

        return $tree;
    }
}
