<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all rule checkers in registry service.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RegisterRuleCheckersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.promotion_rule_checker')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.promotion_rule_checker');
        $checkers = array();

        foreach ($container->findTaggedServiceIds('sylius.promotion_rule_checker') as $id => $attributes) {
                $checkers[$attributes[0]['type']] = $attributes[0]['label'];

            $registry->addMethodCall('registerChecker', array($attributes[0]['type'], new Reference($id)));
        }

        $container->setParameter('sylius.promotion_rules', $checkers);
    }
}
