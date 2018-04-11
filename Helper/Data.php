<?php

namespace Mageplaza\DeleteOrder\Helper;

use Mageplaza\Core\Helper\AbstractData;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractData
{
    const XML_PATH_GENERAL_ENABLED = 'deleteorder/general/is_enabled';

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isEnabled($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GENERAL_ENABLED, $storeId);

    }
}