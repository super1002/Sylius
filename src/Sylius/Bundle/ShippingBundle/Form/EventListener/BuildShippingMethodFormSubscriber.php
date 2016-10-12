<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\EventListener;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BuildShippingMethodFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var FormRegistryInterface
     */
    private $formRegistry;

    /**
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param FormFactoryInterface     $factory
     * @param FormRegistryInterface    $formRegistry
     */
    public function __construct(ServiceRegistryInterface $calculatorRegistry, FormFactoryInterface $factory, FormRegistryInterface $formRegistry)
    {
        $this->calculatorRegistry = $calculatorRegistry;
        $this->factory = $factory;
        $this->formRegistry = $formRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $method = $event->getData();

        if (null === $method || null === $method->getId()) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $method->getCalculator(), $method->getConfiguration());
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('calculator', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['calculator']);
    }

    /**
     * @param FormInterface $form
     * @param string $calculatorName
     * @param array $data
     */
    protected function addConfigurationFields(FormInterface $form, $calculatorName, array $data = [])
    {
        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculatorRegistry->get($calculatorName);

        $calculatorTypeName = sprintf('sylius_shipping_calculator_%s', $calculator->getType());

        if (!$this->formRegistry->hasType($calculatorTypeName)) {
            return;
        }

        $configurationField = $this->factory->createNamed(
            'configuration',
            $calculatorTypeName,
            $data,
            ['auto_initialize' => false]
        );

        $form->add($configurationField);
    }
}
