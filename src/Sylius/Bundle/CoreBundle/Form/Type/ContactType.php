<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'sylius.ui.email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.contact.email.not_blank',
                    ]),
                    new Email([
                        'message' => 'sylius.contact.email.invalid',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'sylius.ui.message',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.contact.message.not_blank',
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
                $email = $options['email'];
                if (null === $email) {
                    return;
                }

                $data = $event->getData();
                $data['email'] = $email;

                $event->setData($data);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'email' => null,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_contact';
    }
}
