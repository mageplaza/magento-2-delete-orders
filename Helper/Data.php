<?php

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