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

namespace Mageplaza\DeleteOrders\Controller\Adminhtml\Order;

use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Controller\Adminhtml\Order;
use Mageplaza\DeleteOrders\Helper\Data;

/**
 * Class Delete
 * @package Mageplaza\DeleteOrders\Controller\Adminhtml\Order
 */
class Delete extends Order
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::delete';

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect  = $this->resultRedirectFactory->create();
        $objectManager   = ObjectManager::getInstance();
        $orderManagement = $objectManager->create('Magento\Sales\Api\OrderManagementInterface');
        $status          = ['processing', 'pending', 'fraud'];
        $helper          = $this->_objectManager->get(Data::class);
        if (!$helper->isEnabled()) {
            $this->messageManager->addError(__('Cannot delete the order.'));

            return $resultRedirect->setPath('sales/order/view', [
                'order_id' => $this->getRequest()->getParam('order_id')
            ]);
        }

        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($helper->versionCompare('2.3.0')) {
                    if (in_array($order->getStatus(), $status)) {
                        $orderManagement->cancel($order->getId());
                    }
                    if ($order->getStatus() === 'holded') {
                        $orderManagement->unHold($order->getId());
                        $orderManagement->cancel($order->getId());
                    }
                }
                /** delete order*/
                $this->orderRepository->delete($order);
                /** delete order data on grid report data related*/
                $helper->deleteRecord($order->getId());

                $this->messageManager->addSuccessMessage(__('The order has been deleted.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('An error occurred while deleting the order. Please try again later.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);

                return $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
            }
        }

        return $resultRedirect->setPath('sales/*/');
    }
}
