<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Component\Report\Model\ReportInterface;
use Symfony\Component\HttpFoundation\NotFoundHttpException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ReportController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderAction(Request $request)
    {
        $report = $this->findOr404($request);

        $formType = sprintf('sylius_data_fetcher_%s', $report->getDataFetcher());
        $configurationForm = $this->get('form.factory')->createNamed('configuration', $formType, $report->getDataFetcherConfiguration());

        if ($request->query->has('configuration')) {
            $configurationForm->submit($request);
        }

        return $this->render($this->config->getTemplate('show.html'), array('report' => $report, 'form' => $configurationForm->createView(), 'configuration' => $configurationForm->getData()));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function embeddAction(Request $request, $report, array $configuration = array())
    {
        if (!$report instanceof ReportInterface) {
            $report = $this->get('sylius.repository.report')->findOneByName($report);
        }

        if (null === $report) {
            throw new NotFoundHttpException('Requested report does not exist.');
        }

        $configuration = $request->query->get('configuration', $configuration);
        $data = $this->get('sylius.report.data_fetcher')->fetch($report, $configuration);

        return $this->get('sylius.report.renderer')->render($report, $data);
    }
}
