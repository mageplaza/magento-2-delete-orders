<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_DeleteOrders
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DeleteOrders\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\ResourceModel\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\DeleteOrders\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'delete_orders';

    /**
     * @var \Magento\Sales\Model\ResourceModel\OrderFactory
     */
    private $orderResourceFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context           $context
     * @param \Magento\Framework\ObjectManagerInterface       $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface      $storeManager
     * @param \Magento\Sales\Model\ResourceModel\OrderFactory $orderResourceFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        OrderFactory $orderResourceFactory
    ) {
        $this->orderResourceFactory = $orderResourceFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param $orderId
     */
    public function deleteRecord($orderId)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order $resource */
        $resource   = $this->orderResourceFactory->create();
        $connection = $resource->getConnection();

        /** delete invoice grid record via resource model*/
        $connection->delete(
            $resource->getTable('sales_invoice_grid'),
            $connection->quoteInto('order_id = ?', $orderId)
        );

        /** delete shipment grid record via resource model*/
        $connection->delete(
            $resource->getTable('sales_shipment_grid'),
            $connection->quoteInto('order_id = ?', $orderId)
        );

        /** delete creditmemo grid record via resource model*/
        $connection->delete(
            $resource->getTable('sales_creditmemo_grid'),
            $connection->quoteInto('order_id = ?', $orderId)
        );

        return;
    }

    /**
     * @param  $days
     *
     * @return false|string
     */
    public function setDate($days)
    {
        return date('Y-m-d H:i:s', strtotime('-' . $days . ' days'));
    }

    /**
     * @param  null $storeId
     *
     * @return mixed
     */
    public function getOrderStatusConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('order_status', $storeId));
    }

    /**
     * @param  null $storeId
     *
     * @return mixed
     */
    public function getOrderCustomerGroupConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('customer_groups', $storeId));
    }

    /**
     * @param  null $storeId
     *
     * @return array
     */
    public function getStoreViewConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('store_views', $storeId));
    }

    /**
     * @param  null $storeId
     *
     * @return mixed
     */
    public function getShippingCountryType($storeId = null)
    {
        return $this->getScheduleConfig('country', $storeId);
    }

    /**
     * @param  null $storeId
     *
     * @return array
     */
    public function getCountriesConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('specific_country', $storeId));
    }

    /**
     * @param  null $storeId
     *
     * @return mixed
     */
    public function getOrderTotalConfig($storeId = null)
    {
        return $this->getScheduleConfig('order_under', $storeId);
    }

    /**
     * @param  null $storeId
     *
     * @return mixed
     */
    public function getPeriodConfig($storeId = null)
    {
        return $this->getScheduleConfig('day_before', $storeId);
    }

    /**
     * @param       $code
     * @param  null $storeId
     *
     * @return mixed
     */
    public function getScheduleConfig($code, $storeId = null)
    {
        return $this->getModuleConfig('schedule/' . $code, $storeId);
    }
}