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
use Magento\Store\Model\System\Store;

/**
 * Class StoreViews
 *
 * @package Mageplaza\DeleteOrders\Model\Config\Source
 */
class StoreViews implements ArrayInterface
{
    /**
     * @var Store
     */
    private $systemStore;

    /**
     * StoreViews constructor.
     *
     * @param Store $systemStore
     */
    public function __construct(Store $systemStore)
    {
        $this->systemStore = $systemStore;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->systemStore->getStoreValuesForForm(false, true);
    }
}
