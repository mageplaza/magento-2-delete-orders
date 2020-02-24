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

namespace Mageplaza\DeleteOrders\Helper;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\DeleteOrders\Helper\Data as HelperData;

/**
 * Class Email
 *
 * @package Mageplaza\DeleteOrders\Helper
 */
class Email extends AbstractData
{
    const CONFIG_MODULE_PATH  = 'delete_orders';
    const EMAIL_CONFIGURATION = '/email';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Email constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        HelperData $helperData
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->_helperData      = $helperData;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param array $templateParams
     * @param null $storeId
     *
     * @return $this
     */
    public function sendEmailTemplate($templateParams = [], $storeId = null)
    {
        try {
            $toEmails = $this->getToEmail($storeId);
            foreach ($toEmails as $toEmail) {
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($this->getTemplate($storeId))
                    ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                    ->setTemplateVars($templateParams)
                    ->setFrom($this->getSender($storeId))
                    ->addTo($toEmail)
                    ->getTransport();

                $transport->sendMessage();
            }
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $this;
    }

    /**
     * ======================================= Email Configuration ==================================================
     *
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigEmail($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . self::EMAIL_CONFIGURATION . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabledEmail($storeId = null)
    {
        if ($this->_helperData->isEnabled()) {
            return (bool) $this->getConfigEmail('enabled', $storeId);
        }

        return false;
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getSender($storeId = null)
    {
        return $this->getConfigEmail('sender', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getTemplate($storeId = null)
    {
        return $this->getConfigEmail('template', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getToEmail($storeId = null)
    {
        return explode(',', $this->getConfigEmail('to', $storeId));
    }
}
