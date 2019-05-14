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
 * @category  Mageplaza
 * @package   Mageplaza_DeleteOrders
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DeleteOrders\Controller\Adminhtml\Delete;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\OrderRepository;
use Mageplaza\DeleteOrders\Helper\Data as HelperData;
use Psr\Log\LoggerInterface;

/**
 * Class Manually
 *
 * @package Mageplaza\DeleteOrders\Controller\Adminhtml\Delete
 */
class Manually extends Action
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Manually constructor.
     * @param Context $context
     * @param HelperData $helperData
     * @param OrderRepository $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        OrderRepository $orderRepository,
        LoggerInterface $logger
    ) {
        $this->_helperData = $helperData;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $storeId = $this->getRequest()->getParam('store');

        $orderCollection = $this->_helperData->getMatchingOrders($storeId);
        if ($orderCollection->getSize()) {
            $successDelete = 0;
            $errorOrders = [];
            foreach ($orderCollection->getItems() as $order) {
                try {
                    $this->orderRepository->delete($order);
                    $this->_helperData->deleteRecord(reset($orderIds));

                    $successDelete++;
                } catch (Exception $e) {
                    $errorOrders[$order->getId()] = $order->getIncrementId();
                    $this->logger->error($e->getMessage());
                }
            }

            if ($successDelete) {
                $this->messageManager->addSuccessMessage(__('Success! ' . $successDelete . ' orders have been deleted'));
            }

            if (count($errorOrders)) {
                $this->messageManager->addSuccessMessage(__('The following orders cannot being deleted. %1', implode(', ', $errorOrders)));
            }
        } else {
            $this->messageManager->addNoticeMessage(__('No order has been deleted!'));
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
