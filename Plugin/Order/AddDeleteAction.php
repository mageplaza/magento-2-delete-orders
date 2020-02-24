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

namespace Mageplaza\DeleteOrders\Plugin\Order;

use Magento\Framework\AuthorizationInterface;
use Magento\Ui\Component\MassAction;
use Mageplaza\DeleteOrders\Helper\Data;

/**
 * Class AddDeleteAction
 * @package Mageplaza\DeleteOrders\Plugin\Order
 */
class AddDeleteAction
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * AddDeleteAction constructor.
     *
     * @param Data $helper
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        Data $helper,
        AuthorizationInterface $authorization
    ) {
        $this->helper         = $helper;
        $this->_authorization = $authorization;
    }

    /**
     * @param MassAction $object
     * @param $result
     *
     * @return mixed
     */
    public function afterGetChildComponents(MassAction $object, $result)
    {
        if (!isset($result['mp_delete'])) {
            return $result;
        }

        if (!$this->helper->isEnabled() || !$this->_authorization->isAllowed('Magento_Sales::delete')) {
            unset($result['mp_delete']);
        }

        return $result;
    }
}
