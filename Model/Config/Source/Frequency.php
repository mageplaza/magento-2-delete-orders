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

namespace Mageplaza\DeleteOrders\Model\Config\Source;

/**
 * Class Frequency
 *
 * @package Mageplaza\DeleteOrders\Model\Config\Source
 */
class Frequency extends \Magento\Cron\Model\Config\Source\Frequency
{
    const DISABLE = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        parent::toOptionArray();

        array_unshift(self::$_options, ['label' => __('Disable'), 'value' => self::DISABLE]);

        return self::$_options;
    }
}
