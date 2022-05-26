<?php

/**
 * @author Jo Ng <ngkwokwah@gmail.com>
 */

namespace Expertime\CustomerDonation\Plugin;

/**
 * Class plugin to add field `donated` to the collection of table "sales_order_grid"
 */
class AddDataToOrdersGrid
{
    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
     * @param $requestName
     * @return mixed
     */
    public function afterGetReport($subject, $collection, $requestName)
    {
        if ($requestName !== 'sales_order_grid_data_source') {
            return $collection;
        }

        if ($collection->getMainTable() === $collection->getResource()->getTable('sales_order_grid')) {
            try {
                $collection->getSelect()->columns(['donated' => new \Zend_Db_Expr("case when donation_amount > 0 then 'Yes' else 'No' end")]);
                $collection->addFilterToMap(
                    'donated',
                    new \Zend_Db_Expr("case when main_table.donation_amount > 0 then 'Yes' else 'No' end")
                );
            } catch (\Zend_Db_Select_Exception $selectException) {
            }
        }

        return $collection;
    }
}
