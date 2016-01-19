<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Translation\TranslationFilesFinder;
use Sylius\Bundle\ThemeBundle\Translation\TranslationFilesFinderInterface;
use Symfony\Component\Finder\Finder;

/**
 * @mixin TranslationFilesFinder
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TranslationFilesFinderSpec extends ObjectBehavior
{
    function let(FinderFactoryInterface $finderFactory)
    {
        $this->beConstructedWith($finderFactory);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Translation\TranslationFilesFinder');
    }

    function it_implements_translation_resource_finder_interface()
    {
        $this->shouldImplement(TranslationFilesFinderInterface::class);
    }

    function it_returns_an_array_of_translation_resources_paths(
        FinderFactoryInterface $finderFactory,
        Finder $finder,
        ThemeInterface $theme
    ) {
        $finderFactory->create()->willReturn($finder);
        $theme->getPath()->willReturn('/theme');

        $finder->in('/theme')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator([
            '/theme/messages.en.yml',
            '/theme/translations/messages.en.yml',
            '/theme/translations/messages.en.yml.jpg',
            '/theme/translations/messages.yml',
            '/theme/AcmeBundle/translations/messages.pl_PL.yml',
        ]));

        $this->findTranslationFiles($theme)->shouldReturn([
            '/theme/translations/messages.en.yml',
            '/theme/AcmeBundle/translations/messages.pl_PL.yml',
        ]);
    }
}
