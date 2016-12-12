<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Taxon;

use Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductTaxonsToTaxonsTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductTaxonAutocompleteChoiceType extends AbstractType
{
    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;

    /**
     * @param FactoryInterface $productTaxonFactory
     */
    public function __construct(FactoryInterface $productTaxonFactory)
    {
        $this->productTaxonFactory = $productTaxonFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ProductTaxonsToTaxonsTransformer($this->productTaxonFactory));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'remote_route' => 'sylius_admin_ajax_taxon_index',
            'remote_criteria_type' => 'contains',
            'remote_criteria_name' => 'name',
            'resource' => 'sylius.taxon',
            'choice_name' => 'name',
            'choice_value' => 'id',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ResourceAutocompleteChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_product_taxon_autocomplete_choice';
    }
}
