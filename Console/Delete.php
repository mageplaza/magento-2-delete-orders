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

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderRepository;
use Mageplaza\DeleteOrders\Helper\Data as HelperData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Delete
 *
 * @package Mageplaza\DeleteOrders\Console
 */
class Delete extends Command
{
    const ORDER_ID = 'order_id';

    /**
     * @var HelperData
     */
    protected $_helperData;

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
     * Delete constructor.
     *
     * @param HelperData $helperData
     * @param OrderRepository $orderRepository
     * @param State $state
     * @param Registry $registry
     * @param null $name
     */
    public function __construct(
        HelperData $helperData,
        OrderRepository $orderRepository,
        state $state,
        Registry $registry,
        $name = null
    ) {
        $this->_helperData     = $helperData;
        $this->orderRepository = $orderRepository;
        $this->state           = $state;
        $this->registry        = $registry;

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

        $this->state->setAreaCode(Area::AREA_ADMINHTML);
        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);

        $orderId = $input->getArgument(self::ORDER_ID);
        try {
            /** delete order*/
            $this->orderRepository->deleteById($orderId);
            /** delete order data on grid report data related*/
            $this->_helperData->deleteRecord($orderId);

            $output->writeln('<info>The delete order process has been successful!</info>');
            return 0;
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
    }
}
