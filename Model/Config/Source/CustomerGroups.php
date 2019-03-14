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

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class CustomerGroups
 *
 * @package Mageplaza\DeleteOrders\Model\Config\Source
 */
class CustomerGroups implements ArrayInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * CustomerGroups constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->collection->toOptionArray();
    }
}
