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

use Magento\Store\Model\System\Store;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class StoreView
 *
 * @package Mageplaza\DeleteOrders\Model\Config\Source
 */
class StoreView extends AbstractSource
{
    /**
     * @var Store
     */
    private $store;

    /**
     * StoreView constructor.
     *
     * @param Store $store
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $this->_options[] = [
            'label' => __('All Store Views'),
            'value' => 0,
        ];

        foreach ($this->store->toOptionArray() as $item) {
            $this->_options[] = [
                'label' => __($item['label']),
                'value' => $item['value'],
            ];
        }

        return $this->_options;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options[] = [
            'label' => __('All Store Views'),
            'value' => 0,
        ];

        foreach ($this->store->toOptionArray() as $item) {
            $this->_options[] = [
                'label' => __($item['label']),
                'value' => $item['value'],
            ];
        }

        return $this->_options;
    }
}
