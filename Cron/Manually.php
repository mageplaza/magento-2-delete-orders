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

namespace Mageplaza\DeleteOrders\Cron;

use Mageplaza\DeleteOrders\Model\ResourceModel\Action;
use Mageplaza\DeleteOrders\Helper\Email;
use Mageplaza\DeleteOrders\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\App\State;

/**
 * Class Manually
 *
 * @package Mageplaza\DeleteOrders\Cron
 */
class Manually
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var Action
     */
    protected $_action;

    /**
     * @var Email
     */
    protected $_email;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var state
     */
    protected $state;

    /**
     * Manually constructor.
     *
     * @param HelperData $helperData
     * @param Action $action
     * @param Email $email
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        HelperData $helperData,
        Action $action,
        Email $email,
        StoreManagerInterface $storeManager,
        OrderRepository $orderRepository,
        state $state
    ) {
        $this->_helperData   = $helperData;
        $this->_action       = $action;
        $this->_email        = $email;
        $this->_storeManager = $storeManager;
        $this->orderRepository   = $orderRepository;
        $this->state = $state;
    }

    /**
     * action cron send email
     */
    public function process()
    {
        foreach ($this->_storeManager->getStores() as $store) {
            if ($this->_email->isEnabledEmail($store->getId())) {

                if (!$this->state->getAreaCode()) {
                    $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
                }
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $objectManager->get('Magento\Framework\Registry')->register('isSecureArea', true);

                $orderIds = $this->_action->getMatchingOrders($store->getId());

                $numberOfOrders = count($orderIds);

                if($numberOfOrders == 1 ) {
                    /** delete order*/
                    $this->orderRepository->deleteById(reset($orderIds));
                    /** delete order data on grid report data related*/
                    $this->_helperData->deleteRecord(reset($orderIds));
                } else {
                    foreach ($orderIds as $id) {
                        /** delete order*/
                        $this->orderRepository->deleteById($id);
                        /** delete order data on grid report data related*/
                        $this->_helperData->deleteRecord($id);
                    }
                }

                $templateParams = [
                    'num_order' => $numberOfOrders
                ];

                $this->_email->sendEmailTemplate($templateParams, $store->getId());
            }
        }
    }
}
