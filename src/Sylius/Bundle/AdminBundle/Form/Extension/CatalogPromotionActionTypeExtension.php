<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Twig\Environment;

final class CatalogPromotionActionTypeExtension extends AbstractTypeExtension
{
    private array $actionTypes = [];

    private array $actionConfigurationTypes;

    private Environment $twig;

    public function __construct(iterable $actionConfigurationTypes, Environment $twig)
    {
        foreach ($actionConfigurationTypes as $type => $formType) {
            $this->actionConfigurationTypes[$type] = get_class($formType);
            $this->actionTypes['sylius.form.catalog_promotion.action.' . $type] = $type;
        }
        ksort($this->actionTypes);

        $this->twig = $twig;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => $this->actionTypes,
                'choice_attr' => function(?string $type) use ($builder): array {
                    return [
                        'data-configuration' => $this->twig->render(
                            '@SyliusAdmin/CatalogPromotion/_action.html.twig',
                            ['field' => $builder->create(
                                'configuration', 
                                $this->actionConfigurationTypes[$type],
                                ['label' => false, 'csrf_protection' => false]
                            )->getForm()->createView()]
                        )
                    ];
                }
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [CatalogPromotionActionType::class];
    }
}
