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

use Magento\Backend\App\Action;
use Mageplaza\DeleteOrders\Model\ResourceModel\Action as OrderAction;
use Mageplaza\DeleteOrders\Helper\Data as HelperData;
use Magento\Sales\Model\OrderRepository;

/**
 * Class Manually
 *
 * @package Mageplaza\DeleteOrders\Controller\Adminhtml\Delete
 */
class Manually extends Action
{
    /**
     * @var \Mageplaza\DeleteOrders\Model\ResourceModel\Action
     */
    protected $_action;

    /**
     * @var \Mageplaza\DeleteOrders\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * Manually constructor.
     *
     * @param \Magento\Backend\App\Action\Context                $context
     * @param \Mageplaza\DeleteOrders\Model\ResourceModel\Action $action
     * @param \Mageplaza\DeleteOrders\Helper\Data                $helperData
     * @param \Magento\Sales\Model\OrderRepository               $orderRepository
     */
    public function __construct(
        Action\Context $context,
        OrderAction $action,
        HelperData $helperData,
        OrderRepository $orderRepository
    ) {
        $this->_action         = $action;
        $this->_helperData     = $helperData;
        $this->orderRepository = $orderRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $storeId        = $this->getRequest()->getParam('store');
        $orderIds       = $this->_action->getMatchingOrders($storeId);

        if ($this->_helperData->isEnabled($storeId)) {
            try {
                $numberOfOrders = count($orderIds);
                if ($numberOfOrders < 1) {
                    $this->messageManager->addSuccessMessage(__('No order has been deleted!'));
                } elseif ($numberOfOrders == 1) {
                    /** delete order*/
                    $this->orderRepository->deleteById(reset($orderIds));
                    /** delete order data on grid report data related*/
                    $this->_helperData->deleteRecord(reset($orderIds));

                    $this->messageManager->addSuccessMessage(__('Success! ' . $numberOfOrders . ' order has been deleted'));
                } else {
                    foreach ($orderIds as $id) {
                        /** delete order*/
                        $this->orderRepository->deleteById($id);
                        /** delete order data on grid report data related*/
                        $this->_helperData->deleteRecord($id);
                    }
                    $this->messageManager->addSuccessMessage(__('Success! ' . $numberOfOrders . ' orders have been deleted'));
                }
            } catch (\Exception $e) {
                $this->messageManager
                    ->addErrorMessage(
                        __('An error occurred while running manually. Please try again later. %1', $e->getMessage())
                    );
            }
        } else {
            $this->messageManager->addNoticeMessage(__('Please enable module!'));
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
