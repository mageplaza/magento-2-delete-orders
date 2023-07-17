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
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\OrderFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\DeleteOrders\Model\Config\Source\Country;

/**
 * Class Data
 * @package Mageplaza\DeleteOrders\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'delete_orders';

    /**
     * @var OrderFactory
     */
    private $orderResourceFactory;

    protected $orderCollectionFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param OrderFactory $orderResourceFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        CollectionFactory $orderCollectionFactory,
        OrderFactory $orderResourceFactory
    ) {
        $this->orderResourceFactory   = $orderResourceFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Get order collection which matching the delete config condition
     *
     * @param null $storeId
     * @param int $limit
     *
     * @return Collection
     */
    public function getMatchingOrders($storeId = null, $limit = 1000)
    {
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('status', ['in' => $this->getOrderStatusConfig($storeId)])
            ->addFieldToFilter('customer_group_id', ['in' => $this->getOrderCustomerGroupConfig($storeId)]);

        $storeIds = $this->getStoreViewConfig($storeId);
        if (!in_array(Store::DEFAULT_STORE_ID, $storeIds, true)) {
            $orderCollection->addFieldToFilter('store_id', ['in' => $storeIds]);
        }

        if ($total = $this->getOrderTotalConfig($storeId)) {
            $orderCollection->addFieldToFilter('base_grand_total', ['lteq' => $total]);
        }

        if ($dayBefore = $this->getPeriodConfig($storeId)) {
            $orderCollection->addFieldToFilter('created_at', ['lteq' => $this->setDate($dayBefore)]);
        }

        if ($limit) {
            $orderCollection->getSelect()->limit($limit);
        }

        if ($this->getShippingCountryType($storeId) === Country::SPECIFIC) {
            $orderCollection->getSelect()
                ->joinLeft(
                    ['soa' => $orderCollection->getTable('sales_order_address')],
                    'main_table.entity_id = soa.parent_id',
                    []
                )
                ->where('soa.country_id IN (?)', $this->getCountriesConfig($storeId))
                ->where('soa.address_type IN (?)', 'shipping');
        }

        return $orderCollection;
    }

    /**
     * @param $orderId
     */
    public function deleteRecord($orderId)
    {
        /** @var Order $resource */
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
     * @param null $storeId
     *
     * @return mixed
     */
    public function getOrderStatusConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('order_status', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getOrderCustomerGroupConfig($storeId = null)
    {
        return explode(',', (string)$this->getScheduleConfig('customer_groups', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getStoreViewConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('store_views', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getShippingCountryType($storeId = null)
    {
        return $this->getScheduleConfig('country', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getCountriesConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('specific_country', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getOrderTotalConfig($storeId = null)
    {
        return $this->getScheduleConfig('order_under', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPeriodConfig($storeId = null)
    {
        return $this->getScheduleConfig('day_before', $storeId);
    }

    /**
     * @param       $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getScheduleConfig($code, $storeId = null)
    {
        return $this->getModuleConfig('schedule/' . $code, $storeId);
    }
}
