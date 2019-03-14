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

namespace Mageplaza\DeleteOrders\Console;

use Mageplaza\DeleteOrders\Helper\Data as HelperData;
use Mageplaza\DeleteOrders\Model\ResourceModel\Action;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\App\State;

/**
 * Class Delete
 *
 * @package Mageplaza\DeleteOrders\Console
 */
class Delete extends Command
{
    const ORDER_ID = 'order_id';

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
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * Delete constructor.
     *
     * @param \Mageplaza\DeleteOrders\Model\ResourceModel\Action $action
     * @param \Mageplaza\DeleteOrders\Helper\Data                $helperData
     * @param null                                               $name
     * @param \Magento\Sales\Model\OrderRepository               $orderRepository
     * @param \Magento\Framework\App\State                       $state
     */
    public function __construct(
        Action $action,
        HelperData $helperData,
        $name = null,
        OrderRepository $orderRepository,
        state $state
    ) {
        $this->_action         = $action;
        $this->_helperData     = $helperData;
        $this->orderRepository = $orderRepository;
        $this->state           = $state;

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('order:delete')
            ->setDescription('Delete order by id')
            ->addArgument(self::ORDER_ID, InputArgument::OPTIONAL, __('Order Id'));

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->_helperData->isEnabled()) {
            $output->writeln('<error>Please enable the module.</error>');

            return;
        }
        if (!$this->state->getAreaCode()) {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->get('Magento\Framework\Registry')->register('isSecureArea', true);

        $orderId = $input->getArgument(self::ORDER_ID);
        if (in_array($orderId, $this->_action->getAllOrderIds(), true)) {
            try {
                /** delete order*/
                $this->orderRepository->deleteById($orderId);
                /** delete order data on grid report data related*/
                $this->_helperData->deleteRecord($orderId);

                $output->writeln('<info>The delete order process has been successful!</info>');
            } catch (\Exception $e) {
                $output->writeln("<error>{$e->getMessage()}</error>");
            }
        } else {
            $output->writeln('<error>The order ID has not been found!</error>');
        }
    }
}
