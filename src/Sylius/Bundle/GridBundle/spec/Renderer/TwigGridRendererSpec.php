<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\GridBundle\Renderer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\GridBundle\Renderer\TwigGridRenderer;
use Sylius\Component\Grid\ActionTypes;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @mixin TwigGridRenderer
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TwigGridRendererSpec extends ObjectBehavior
{
    function let(\Twig_Environment $twig, ServiceRegistryInterface $fieldsRegistry)
    {
        $actionTemplates = [
            ActionTypes::LINK => 'SyliusGridBundle:Action:_link.html.twig',
            ActionTypes::FORM => 'SyliusGridBundle:Action:_form.html.twig',
        ];

        $this->beConstructedWith($twig, 'SyliusGridBundle:default.html.twig', $fieldsRegistry, $actionTemplates);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\GridBundle\Renderer\TwigGridRenderer');
    }
    
    function it_is_a_grid_renderer()
    {
        $this->shouldImplement(GridRendererInterface::class);
    }
    
    function it_uses_Twig_to_render_the_grid_view(\Twig_Environment $twig, GridView $gridView)
    {
        $twig->render('SyliusGridBundle:default.html.twig', ['grid' => $gridView])->willReturn('<html>Grid!</html>');
        $this->render($gridView)->shouldReturn('<html>Grid!</html>');
    }

    function it_uses_custom_template_if_specified(\Twig_Environment $twig, GridView $gridView)
    {
        $twig->render('SyliusGridBundle:custom.html.twig', ['grid' => $gridView])->willReturn('<html>Grid!</html>');
        $this->render($gridView, 'SyliusGridBundle:custom.html.twig')->shouldReturn('<html>Grid!</html>');
    }

    function it_uses_Twig_to_render_the_action(\Twig_Environment $twig, GridView $gridView, Action $action)
    {
        $action->getType()->willReturn(ActionTypes::LINK);
        $twig->render('SyliusGridBundle:Action:_link.html.twig', ['grid' => $gridView, 'action' => $action, 'data' => null])->willReturn('<a href="#">Action!</a>');

        $this->renderAction($gridView, $action)->shouldReturn('<a href="#">Action!</a>');
    }

    function it_renders_field_with_data_via_appriopriate_field_type(
        GridView $gridView,
        Field $field,
        ServiceRegistryInterface $fieldsRegistry,
        FieldTypeInterface $fieldType
    ) {
        $field->getType()->willReturn('string');
        $fieldsRegistry->get('string')->willReturn($fieldType);
        $fieldType->render($field, 'Value')->willReturn('<strong>Value</strong>');

        $this->renderField($gridView, $field, 'Value')->shouldReturn('<strong>Value</strong>');
    }

    function it_throws_an_exception_if_template_is_not_configured_for_given_action_type(GridView $gridView, Action $action)
    {
        $action->getType()->willReturn('foo');

        $this
            ->shouldThrow(new \InvalidArgumentException('Missing template for action type "foo".'))
            ->during('renderAction', [$gridView, $action])
        ;
    }
}
