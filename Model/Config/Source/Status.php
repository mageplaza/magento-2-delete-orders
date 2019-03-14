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

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 *
 * @package Mageplaza\DeleteOrders\Model\Config\Source
 */
class Status implements ArrayInterface
{
    const CANCELED   = 'canceled';
    const CLOSED     = 'closed';
    const COMPLETE   = 'complete';
    const FRAUD      = 'fraud';
    const ON_HOLD    = 'holded';
    const PENDING    = 'pending';
    const PROCESSING = 'processing';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function toArray()
    {
        return [
            0                => __('-- Please Select --'),
            self::CANCELED   => __('Canceled'),
            self::CLOSED     => __('Closed'),
            self::COMPLETE   => __('Complete'),
            self::FRAUD      => __('Suspected Fraud'),
            self::ON_HOLD    => __('On Hold'),
            self::PENDING    => __('Pending'),
            self::PROCESSING => __('Processing'),
        ];
    }
}
