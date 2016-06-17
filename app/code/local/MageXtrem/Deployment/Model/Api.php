<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/06/16
 * Time: 10:13
 */
class MageXtrem_Deployment_Model_Api extends Mage_Api_Model_Resource_Abstract
{

    /**
     * @var MageXtrem_Deployment_Helper_Data
     */
    protected $_helper;

    /**
     * @var MageXtrem_Deployment_Helper_Logger
     */
    protected $_logger;

    public function __construct()
    {
        $this->_helper = Mage::helper('magextrem_deployment');
        $this->_logger = Mage::helper('magextrem_deployment/logger');
    }

    /**
     * @return bool
     */
    public function push()
    {
        $this->_logger->log('Checking data & IP');
        // Check remote address authorization
        if ($this->_helper->checkRemoteAddress()) {
            $this->_logger->log('Authorized IP : ' . Mage::helper('magextrem_utils/env')->getIP());
            $payload = $this->_helper->getInputPayload();

            // Check input payload data
            if (empty($payload)) {
                $this->_logger->log('Skipping payload, empty data');
                $this->_logger->logPayload($payload);
                return false;
            }
            if (!isset($payload->repository->name, $payload->push->changes)) {
                $this->_logger->log('Skipping payload, invalid data');
                $this->_logger->logPayload($payload);
                return false;
            }
            if (!$this->checkBranchChanges($payload)) {
                $this->_logger->log('No changes for branch ' . Mage::getStoreConfig(MageXtrem_Deployment_Helper_Data::BRANCH_NAME_XML_PATH));
                $this->_logger->logPayload($payload);
                return false;
            }

            $this->_logger->log('Push type : '.$this->_helper->getPushType());
            // Save deploy flag (Cron will run checkout process)
            if ($this->_helper->getPushType() == MageXtrem_Deployment_Model_Source_Push_Type::CRON_PUSH_TYPE && !$this->_helper->checkCurrentPayload()) {
                $payload = Zend_Json::encode($payload);
                Mage::getConfig()->saveConfig(MageXtrem_Deployment_Helper_Data::PAYLOAD_XML_PATH, $payload);
                $this->_logger->log('Scheduling CRON');
                $this->_logger->logPayload($payload);
                return false;
            }
            // Run checkout process now
            elseif ($this->_helper->getPushType() == MageXtrem_Deployment_Model_Source_Push_Type::DIRECT_PUSH_TYPE) {
                $this->_logger->log('Running deploy process');
                $this->_logger->logPayload($payload);
                Mage::getModel('magextrem_deployment/deploy')->run();
            }
            $this->_logger->log('Success');
            return true;
        }
        $this->_logger->log('Unauthorized IP : ' . Mage::helper('magextrem_utils/env')->getIP(), Zend_Log::ALERT);
        return false;
    }

    /**
     * @param stdClass $payload
     * @return bool
     */
    private function checkBranchChanges($payload)
    {
        foreach ($payload->push->changes as $change) {
            foreach (array('old', 'new') as $direction) {
                if ($change->$direction->type == 'branch' && $change->$direction->name == Mage::getStoreConfig(MageXtrem_Deployment_Helper_Data::BRANCH_NAME_XML_PATH)) {
                    return true;
                }
            }
        }
        return false;
    }

}