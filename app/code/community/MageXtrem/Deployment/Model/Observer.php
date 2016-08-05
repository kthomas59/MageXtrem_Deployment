<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/06/16
 * Time: 17:13
 */
class MageXtrem_Deployment_Model_Observer
{

    /**
     * @var MageXtrem_Deployment_Helper_Data
     */
    protected $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('magextrem_deployment');
    }

    public function checkDeployment($schedule = null)
    {
        if ($this->_helper->checkCurrentPayload()) {
            Mage::getModel('magextrem_deployment/deploy')->run();
        } else {
            Mage::helper('magextrem_deployment/logger')->log('empty payload');
        }
    }

}