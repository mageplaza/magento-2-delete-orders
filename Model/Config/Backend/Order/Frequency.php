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

namespace Mageplaza\DeleteOrders\Model\Config\Backend\Order;

use Exception;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Mageplaza\DeleteOrders\Model\Config\Source\Frequency as ValueConfig;

/**
 * Class Frequency
 *
 * @package Mageplaza\DeleteOrders\Model\Config\Backend\Order
 */
class Frequency extends Value
{
    /**
     * Cron string path
     */
    const CRON_STRING_PATH = 'crontab/default/jobs/delete_orders_cron_manually_email/schedule/cron_expr';
    /**
     * Cron model path
     */
    const CRON_MODEL_PATH = 'crontab/default/jobs/delete_orders_cron_manually_email/run/model';

    /**
     * @var ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Frequency constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ValueFactory $configValueFactory
     * @param ManagerInterface $messageManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ValueFactory $configValueFactory,
        ManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        $runModelPath = '',
        array $data = []
    ) {
        $this->_runModelPath       = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        $this->messageManager      = $messageManager;

        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return Value
     */
    public function afterSave()
    {
        $time      = $this->getData('groups/schedule/fields/time/value');
        $frequency = $this->getData('groups/schedule/fields/frequency/value');

        if ($frequency != (string)ValueConfig::DISABLE) {
            $cronExprArray = [
                (int) $time[1], //Minute
                (int) $time[0], //Hour
                $frequency === \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY ? '1' : '*', //Day of the Month
                '*', //Month of the Year
                $frequency === \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY ? '1' : '*', //Day of the Week
            ];

            $cronExprString = join(' ', $cronExprArray);

            try {
                $this->_configValueFactory->create()
                    ->load(self::CRON_STRING_PATH, 'path')
                    ->setValue($cronExprString)
                    ->setPath(self::CRON_STRING_PATH)
                    ->save();

                $this->_configValueFactory->create()
                    ->load(self::CRON_MODEL_PATH, 'path')
                    ->setValue($this->_runModelPath)
                    ->setPath(self::CRON_MODEL_PATH)
                    ->save();
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t save the cron expression. %1', $e->getMessage()));
            }
        }

        return parent::afterSave();
    }
}
