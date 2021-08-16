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
 * @package     Mageplaza_DeleteOrder
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DeleteOrders\Plugin\Model;

use Mageplaza\DeleteOrders\Helper\Data;
use Magento\Sales\Model\Order as CoreOrder;

/**
 * Class Order
 * @package Mageplaza\DeleteOrders\Plugin\Model
 */
class Order
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Order constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param CoreOrder $order
     * @return array
     */
    public function beforeUnhold(CoreOrder $order)
    {
        if (!$this->helper->isEnabled()) {
            return [];
        }

        if ($order->getStatus() !== $order->getState() && $order->getStatus() === CoreOrder::STATE_HOLDED) {
            $order->setState(CoreOrder::STATE_HOLDED);
        }

        return [];
    }
}
