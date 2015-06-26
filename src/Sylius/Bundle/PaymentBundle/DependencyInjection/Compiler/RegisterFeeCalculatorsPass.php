<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class RegisterFeeCalculatorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.payment.fee_calculator')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.payment.fee_calculator');
        $calculators = array();

        foreach ($container->findTaggedServiceIds('sylius.payment.fee_calculator') as $id => $attributes) {
            if (!isset($attributes[0]['calculator']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged fee calculators needs to have `fee_calculator` and `label` attributes.');
            }

            $name = $attributes[0]['calculator'];
            $calculators[$name] = $attributes[0]['label'];

            $registry->addMethodCall('register', array($name, new Reference($id)));
        }

        $container->setParameter('sylius.payment.fee_calculators', $calculators);
    }
}
