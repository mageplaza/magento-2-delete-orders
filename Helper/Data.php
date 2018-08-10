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

namespace Mageplaza\DeleteOrders\Helper;

use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\DeleteOrders\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'delete_orders';

    /**
     * @param $order
     */
    public function deleteRelatedOrderData($order){
        if($order->hasInvoices()){
            foreach ($order->getInvoiceCollection() as $invoice) {
                $invoice->delete();
            }
        }

        if($order->hasShipments()){
            foreach ($order->getShipmentsCollection() as $shipment) {
                $shipment->delete();
            }
        }

        if($order->hasCreditmemos()){
            foreach ($order->getCreditmemosCollection() as $creditmemo) {
                $creditmemo->delete();
            }
        }
    }
}