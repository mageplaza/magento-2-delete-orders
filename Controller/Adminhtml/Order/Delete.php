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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DeleteOrder\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as DbAC;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Controller\ResultFactory;
use Mageplaza\DeleteOrder\Helper\Data as DataHelper;


class Delete extends AbstractMassAction
{
    protected $orderRepository;
    protected $helper;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderRepository $orderRepository,
        DataHelper $dataHelper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory    = $collectionFactory;
        $this->orderRepository      = $orderRepository;
        $this->helper               = $dataHelper;
    }

    protected function massAction(DbAC $collection)
    {
        if ($this->helper->isEnabled()) {
            $orderDeleted = 0;
            foreach ($collection as $order) {
                $this->orderRepository->deleteById($order->getId());
                $orderDeleted++;
            }
            if ($orderDeleted) {
                $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $orderDeleted));
            }
        } else {
            $this->messageManager->addError(__('Delete Order module is Disabled'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

}