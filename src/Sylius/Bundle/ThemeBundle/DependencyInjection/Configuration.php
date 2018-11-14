<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\DependencyInjection;

use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationSourceFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /** @var ConfigurationSourceFactoryInterface[] */
    private $configurationSourceFactories;

    /**
     * @param ConfigurationSourceFactoryInterface[] $configurationSourceFactories
     */
    public function __construct(array $configurationSourceFactories = [])
    {
        $this->configurationSourceFactories = $configurationSourceFactories;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_theme');

        $this->addSourcesConfiguration($rootNode);

        $rootNode
            ->children()
                ->arrayNode('assets')
                    ->canBeDisabled()
                ->end()
                ->arrayNode('templating')
                    ->canBeDisabled()
                ->end()
                ->arrayNode('translations')
                    ->canBeDisabled()
                ->end()
                ->scalarNode('context')
                    ->defaultValue('sylius.theme.context.settable')
                    ->cannotBeEmpty()
                ->end()
        ;

        return $treeBuilder;
    }

    private function addSourcesConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $sourcesNodeBuilder = $rootNode
            ->fixXmlConfig('source')
                ->children()
                    ->arrayNode('sources')
                            ->children()
        ;

        foreach ($this->configurationSourceFactories as $sourceFactory) {
            $sourceNode = $sourcesNodeBuilder
                ->arrayNode($sourceFactory->getName())
                ->canBeEnabled()
            ;

            $sourceFactory->buildConfiguration($sourceNode);
        }
    }
}
