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

namespace Sylius\Behat\Page\Admin\Shipment;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function chooseStateToFilter(string $shipmentState): void
    {
        $this->getElement('filter_state')->selectOption($shipmentState);
    }

    public function shipShipmentWithSpecificOrderNumber(string $orderNumber): void
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['number' => $orderNumber]);

        $row = $tableAccessor->getRowWithFields($table, ['number' =>$orderNumber]);

        $field = $tableAccessor->getFieldFromRow($table, $row, 'actions');
        $field->pressButton('Ship');
    }
    
    public function getRowWithSpecificOrderNumber(string $orderNumber): string
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');
        $row = $tableAccessor->getRowWithFields($table, ['number' =>$orderNumber]);
        $field = $tableAccessor->getFieldFromRow($table, $row, 'state');

        return $field->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_state' => '#criteria_state',
        ]);
    }
}
