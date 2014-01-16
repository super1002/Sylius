<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sylius\Bundle\PromotionsBundle\Form\DataTransformer\TaxonToIdentifierTransformer;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Taxonomy rule configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonomyConfigurationType extends AbstractType
{
    protected $validationGroups;
    private $taxonRepository;

    public function __construct(array $validationGroups, ObjectRepository $taxonRepository)
    {
        $this->validationGroups = $validationGroups;
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxons', 'sylius_taxon_selection', array(
                'label'             => 'sylius.form.rule.taxonomy_configuration.taxons',
                'model_transformer' => 'Sylius\Bundle\TaxonomiesBundle\Form\DataTransformer\TaxonSelectionToIdentifierCollectionTransformer',
            ))
            ->add('exclude', 'checkbox', array(
                'label' => 'sylius.form.rule.taxonomy_configuration.exclude',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_taxonomy_configuration';
    }
}
