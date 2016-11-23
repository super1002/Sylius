<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Shipping\Calculator;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ChannelBasedFlatRateConfigurationType extends AbstractType
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var ChannelInterface $channel
         */
        foreach ($this->channelRepository->findAll() as $channel) {
            $builder
                ->add($channel->getCode(), 'sylius_shipping_calculator_flat_rate', [
                    'label' => $channel->getName(),
                ])
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_channel_based_shipping_calculator_flat_rate';
    }
}
