<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\FormBuilder;
use Prophecy\Argument;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ChartConfigurationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\Form\Type\ChartConfigurationType');
    }

    function it_should_be_abstract_type_object()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_renderer_chart');
    }

    function it_builds_form_with_type_choice_and_template_choice(FormBuilder $builder)
    {   
        $builder->add('type', 'choice', Argument::any())->willReturn($builder);
        $builder->add('template', 'choice', Argument::any())->willReturn($builder);

        $this->buildForm($builder, array());
    }
}