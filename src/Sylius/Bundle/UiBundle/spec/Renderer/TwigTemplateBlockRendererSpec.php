<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\UiBundle\Renderer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

final class TwigTemplateBlockRendererSpec extends ObjectBehavior
{
    function let(Environment $twig, ContainerInterface $container): void
    {
        $this->beConstructedWith($twig, $container);
    }

    function it_is_a_template_block_renderer(): void
    {
        $this->shouldImplement(TemplateBlockRendererInterface::class);
    }

    function it_renders_a_template_block(Environment $twig, ContainerInterface $container, ContextProviderInterface $contextProvider): void
    {
        $twig->render('block.txt.twig', ['sample' => 'Hello', 'switch' => true])->willReturn('Block content');
        $container->get(ContextProviderInterface::class)->willReturn($contextProvider);

        $contextProvider->provide(['sample' => 'Hello', 'switch' => true], ['sample' => 'Hi', 'switch' => true])->willReturn(['sample' => 'Hello', 'switch' => true]);

        $this->render(
            new TemplateBlock('block_name', 'event_name', 'block.txt.twig', ['sample' => 'Hi', 'switch' => true], ContextProviderInterface::class, 0, true),
            ['sample' => 'Hello', 'switch' => true],
        )->shouldReturn('Block content');
    }
}
