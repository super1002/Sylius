<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Renderer;

use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Report\Renderer\RendererInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Report\Renderer\DefaultRenderers;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TableRenderer implements RendererInterface
{
    private $templating;

    public function __construct(EngineInterface $templating)
    {  
        $this->templating = $templating;
    }   

    public function render(ReportInterface $report, Data $data)
    {   
        if (null !== $data->getData()) {
            $data = array(
                'report' => $report,
                'values' => $data->getData(),
                'labels' => $data->getLabels(),
                'fields' => array_keys($data->getData())
            );   
            return $this->templating->renderResponse($report->getRendererConfiguration()["template"], array('data' => $data, 'configuration' => $report->getRendererConfiguration()));
        }
        return $this->templating->renderResponse("SyliusReportBundle::noDataTemplate.html.twig", array('report' => $report));;
    }

    public function getType()
    {
        return DefaultRenderers::TABLE;
    }
}