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

namespace Mageplaza\DeleteOrders\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Manually
 *
 * @package Mageplaza\DeleteOrders\Block\Adminhtml\System\Config
 */
class Manually extends Field
{
    protected $_template = 'Mageplaza_DeleteOrders::manually.phtml';

    /**
     * @param AbstractElement $element
     *
     * @return string
     * @SuppressWarnings(Unused)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }
}
