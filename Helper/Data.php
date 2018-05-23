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
 * @category    Mageplaza
 * @package     Mageplaza_DeleteOrder
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DeleteOrder\Helper;

use Mageplaza\Core\Helper\AbstractData;

class Data extends AbstractData
{
    const XML_PATH_GENERAL_ENABLED = 'deleteorder/general/is_enabled';

    public function isEnabled($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GENERAL_ENABLED, $storeId);

    }
}