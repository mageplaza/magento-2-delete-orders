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

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\DeleteOrders\Helper\Data as HelperData;
use Mageplaza\DeleteOrders\Helper\Email;
use Psr\Log\LoggerInterface;

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
     * @var Registry
     */
    protected $registry;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Manually constructor.
     * @param HelperData $helperData
     * @param Email $email
     * @param StoreManagerInterface $storeManager
     * @param OrderRepository $orderRepository
     * @param State $state
     * @param Registry $registry
     * @param LoggerInterface $logger
     */
    public function __construct(
        HelperData $helperData,
        Email $email,
        StoreManagerInterface $storeManager,
        OrderRepository $orderRepository,
        state $state,
        Registry $registry,
        LoggerInterface $logger
    ) {
        $this->_helperData = $helperData;
        $this->_email = $email;
        $this->_storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
        $this->state = $state;
        $this->registry = $registry;
        $this->logger = $logger;
    }

    /**
     * action cron send email
     */
    public function process()
    {
        foreach ($this->_storeManager->getStores() as $store) {
            $storeId = $store->getId();
            if (!$this->_helperData->isEnabled($storeId)) {
                continue;
            }

            $orderCollection = $this->_helperData->getMatchingOrders($storeId);
            if ($numOfOrders = $orderCollection->getSize()) {
                $this->registry->unregister('isSecureArea');
                $this->registry->register('isSecureArea', true);

                $errorOrders = [];
                foreach ($orderCollection->getItems() as $order) {
                    try {
                        $this->orderRepository->delete($order);
                        $this->_helperData->deleteRecord(reset($orderIds));
                    } catch (Exception $e) {
                        $errorOrders[$order->getId()] = $order->getIncrementId();
                        $this->logger->error($e->getMessage());
                    }
                }

                if ($this->_email->isEnabledEmail($storeId)) {
                    if (!$this->state->getAreaCode()) {
                        $this->state->setAreaCode(Area::AREA_FRONTEND);
                    }

                    $templateParams = [
                        'num_order'     => $numOfOrders,
                        'success_order' => $numOfOrders - count($errorOrders),
                        'error_order'   => $errorOrders
                    ];

                    $this->_email->sendEmailTemplate($templateParams, $storeId);
                }
            }
        }
    }
}
