<?php

namespace Mageplaza\DeleteOrder\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as DbAC;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\ResourceModel\Grid;


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
        OrderRepository $orderRepository
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory    = $collectionFactory;
        $this->orderRepository      = $orderRepository;
    }

    protected function massAction(DbAC $collection)
    {
        $objectManager     = \Magento\Framework\App\ObjectManager::getInstance();
        $helper        = $objectManager->get('Mageplaza\DeleteOrder\Helper\Data');
        if ($helper->isEnabled()) {
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