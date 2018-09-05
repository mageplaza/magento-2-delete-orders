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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DeleteOrders\Model\Sales\Delete;

use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceGridResourceModel;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentGridResourceModel;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo as CreditmemoGridResourceModel;

/**
 * Class Grid
 * @package Mageplaza\DeleteOrders\Model\Sales\Delete
 */
class Grid extends AbstractModel
{
    /**
     * @var OrderResourceModel
     */
    protected $_orderResourceModel;

    /**
     * @var InvoiceGridResourceModel
     */
    protected $_invoiceResourceModel;

    /**
     * @var ShipmentGridResourceModel
     */
    protected $_shipmentResourceModel;

    /**
     * @var CreditmemoGridResourceModel
     */
    protected $_creditmemoResourceModel;

    /**
     * Grid constructor.
     * @param OrderResourceModel $orderResourceModel
     * @param InvoiceGridResourceModel $invoiceResourceModel
     * @param ShipmentGridResourceModel $shipmentResourceModel
     * @param CreditmemoGridResourceModel $creditmemoResourceModel
     */
    public function __construct(
        OrderResourceModel $orderResourceModel,
        InvoiceGridResourceModel $invoiceResourceModel,
        ShipmentGridResourceModel $shipmentResourceModel,
        CreditmemoGridResourceModel $creditmemoResourceModel
    )
    {
        $this->_orderResourceModel = $orderResourceModel;
        $this->_invoiceResourceModel = $invoiceResourceModel;
        $this->_shipmentResourceModel = $shipmentResourceModel;
        $this->_creditmemoResourceModel = $creditmemoResourceModel;
    }

    /**
     * @param $order
     */
    public function deleteRecord($orderId)
    {
        /** delete invoice grid record via resource model*/
        $this->_invoiceResourceModel->getConnection()->delete(
            $this->_invoiceResourceModel->getTable('sales_invoice_grid'),
            $this->_invoiceResourceModel->getConnection()->quoteInto('order_id = ?', $orderId)
        );

        /** delete shipment grid record via resource model*/
        $this->_shipmentResourceModel->getConnection()->delete(
            $this->_shipmentResourceModel->getTable('sales_shipment_grid'),
            $this->_shipmentResourceModel->getConnection()->quoteInto('order_id = ?', $orderId)
        );

        /** delete creditmemo grid record via resource model*/
        $this->_creditmemoResourceModel->getConnection()->delete(
            $this->_creditmemoResourceModel->getTable('sales_creditmemo_grid'),
            $this->_creditmemoResourceModel->getConnection()->quoteInto('order_id = ?', $orderId)
        );

        return;
    }


}
