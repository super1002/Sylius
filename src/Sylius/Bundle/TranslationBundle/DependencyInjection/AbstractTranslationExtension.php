<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This extension parses the translatable and translation configuration into
 * the doctrine mapping that will be used by the TranslatableListener
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
abstract class AbstractTranslationExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_translation')) {
            throw new ServiceNotFoundException('SyliusTranslationBundle must be registered in kernel.');
        }

        // If the default mapping parameter has already been defined we don't need to do anything
        if ($container->hasParameter('sylius.translation.default.mapping')) {
            return;
        }

        // Parse sylius_translation to get the default mapping values and assign them to
        // 'sylius.translation.default.mapping' parameter to be used un process) method.
        $configs = $container->getExtensionConfig('sylius_translation');
        $config  = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('sylius.translation.default.mapping', $config['default_mapping']);
    }

    /**
     * In case any extra processing is needed.
     *
     * @param array            $class
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    protected function processTranslations(array $class, ContainerBuilder $container)
    {
        if (!($container->hasParameter('sylius.translation.mapping')
            && $translationsMapping = $container->getParameter('sylius.translation.mapping'))
        ) {
            $translationsMapping = array();
        }

        if (!($container->hasParameter('sylius.translation.default.mapping')
            && $defaultValues = $container->getParameter('sylius.translation.default.mapping'))
        ) {
            throw new \Exception('Missing parameter sylius.translation.default.mapping. Default translation mapping must be defined!');
        }

        if (isset($class['translatable'])) {
            $translationsMapping = $this->mapTranslatable(
                $translationsMapping, $class['model'],
                $class['translatable'],
                $defaultValues
            );
        } elseif (isset($class['translation'])) {
            $translationsMapping = $this->mapTranslation(
                $translationsMapping,
                $class['model'],
                $class['translation']
            );
        }

        $container->setParameter('sylius.translation.mapping', $translationsMapping);
    }

    /**
     * Set translatable entity mapping metadata
     *
     * @param array  $translationsMapping
     * @param string $translatableClass
     * @param array  $translatableConfig
     * @param array  $defaultValues
     *
     * @internal param string $translatable
     * @internal param ContainerBuilder $container
     *
     * @return array
     */
    protected function mapTranslatable(array $translationsMapping, $translatableClass, array $translatableConfig, array $defaultValues)
    {
        // Map translatable target entity
        $translationClass = isset($translatableConfig['targetEntity']) ? $translatableConfig['targetEntity'] : $translatableClass.'Translation';
        $translatableConfig['targetEntity'] = $translationClass;

        $translationMetadata = array_merge($defaultValues['translatable'], $translatableConfig);

        $translationsMapping[$translatableClass] = $translationMetadata;

        // Mapping for translation entity with default values
        $translationMetadata  = array_merge($defaultValues['translation'], array('targetEntity' => $translatableClass));
        $translationsMapping[$translationClass] = $translationMetadata;

        return $translationsMapping;
    }

    /**
     * Set translation entity mapping metadata
     *
     * @param array  $translationMapping
     * @param string $translation
     * @param array  $translationConfig
     *
     * @return array
     */
    protected function mapTranslation(array $translationMapping, $translation, array $translationConfig)
    {
        // At this point we already have the default values mapped, so we only need to override them
        // if specific values have been set in entity_translation configuration key
        if (isset($translationConfig['translatable'])) {
            $translationMapping[$translation]['field'] = $translationConfig['translatable'];
        }

        if (isset($translationConfig['locale'])) {
            $translationMapping[$translation]['locale'] = $translationConfig['locale'];
        }

        return $translationMapping;
    }
}
