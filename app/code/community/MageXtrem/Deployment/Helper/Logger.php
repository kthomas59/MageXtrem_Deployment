<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/06/16
 * Time: 16:42
 */
class MageXtrem_Deployment_Helper_Logger extends MageXtrem_Utils_Helper_Logger
{

    public function __construct()
    {
        $this->_active = (bool)Mage::getStoreConfig('magextrem_utils/deployment/debug');
        $this->_filename = 'magextrem/deployment';
    }

    /**
     * @param array|string $payload
     */
    public function logPayload($payload) {
        if (is_array($payload) || $payload instanceof stdClass) {
            $payload = Zend_Json::encode($payload);
        }
        $this->log('payload : ' . $payload);
    }

}