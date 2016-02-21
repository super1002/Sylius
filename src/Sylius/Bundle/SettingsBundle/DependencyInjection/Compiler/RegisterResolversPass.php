<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class RegisterResolversPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.settings.resolver_registry')) {
            return;
        }

        $resolverRegistry = $container->getDefinition('sylius.settings.resolver_registry');

        foreach ($container->findTaggedServiceIds('sylius.settings_resolver') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!array_key_exists('schema', $attributes)) {
                    throw new \InvalidArgumentException(sprintf('Service "%s" must define the "schema" attribute on "sylius.settings_resolver" tags.', $id));
                }

                $schemaAlias = $attributes['schema'];
                $resolverRegistry->addMethodCall('register', [$schemaAlias, new Reference($id)]);

            }
        }
    }
}
