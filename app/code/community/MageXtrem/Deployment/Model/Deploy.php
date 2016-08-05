<?php

/**
 * Created by PhpStorm.+
 * User: kevin
 * Date: 10/06/16
 * Time: 17:23
 */
class MageXtrem_Deployment_Model_Deploy extends Mage_Core_Model_Abstract
{

    /**
     * @var string
     */
    private $repo_dir;

    /**
     * @var string
     */
    private $git_bin_path;

    /**
     * @var string
     */
    private $web_root_dir;

    /**
     * @var string
     */
    private $email;

    /**
     * @var MageXtrem_Deployment_Helper_Data
     */
    protected $_helper;

    /**
     * @var MageXtrem_Deployment_Helper_Logger
     */
    protected $_logger;

    /**
     * @var MageXtrem_Utils_Helper_Mailer
     */
    protected $_mailer;

    protected function _construct()
    {
        parent::_construct();
        $this->repo_dir = Mage::getStoreConfig(MageXtrem_Deployment_Helper_Data::REPO_DIR_XML_PATH);
        $this->git_bin_path = Mage::getStoreConfig(MageXtrem_Deployment_Helper_Data::GIT_BIN_PATH_XML_PATH);
        $this->web_root_dir = Mage::getStoreConfig(MageXtrem_Deployment_Helper_Data::WEB_ROOT_DIR_XML_PATH) ? : Mage::getBaseDir();
        $this->email = Mage::getStoreConfig(MageXtrem_Deployment_Helper_Data::EMAIL_XML_PATH) ? explode(',', Mage::getStoreConfig(MageXtrem_Deployment_Helper_Data::EMAIL_XML_PATH)) : array();
        $this->_helper = Mage::helper('magextrem_deployment');
        $this->_logger = Mage::helper('magextrem_deployment/logger');
        $this->_mailer = Mage::helper('magextrem_utils/mailer');
    }

    /**
     * Run full checkout process
     */
    public function run()
    {
        $this->logProcess()
            ->checkout()
            ->mailProcess()
            ->cleanPayload();
    }

    /**
     * @return $this
     */
    private function checkout()
    {
        exec('cd ' . $this->repo_dir . ' && ' . $this->git_bin_path . ' fetch');
        exec('cd ' . $this->repo_dir . ' && GIT_WORK_TREE=' . $this->web_root_dir . ' ' . $this->git_bin_path . ' checkout -f');

        return $this;
    }

    /**
     * @return $this
     */
    private function logProcess()
    {
        $this->_logger->log('Deployment on ' . $this->web_root_dir);
        $this->_logger->log('cd ' . $this->repo_dir . ' && ' . $this->git_bin_path . ' fetch');
        $this->_logger->log('cd ' . $this->repo_dir . ' && GIT_WORK_TREE=' . $this->web_root_dir . ' ' . $this->git_bin_path . ' checkout -f');
        $this->_logger->log('Commit : ' . $this->getCommitHash());

        return $this;
    }

    /**
     * @return string
     */
    private function getCommitHash()
    {
        return shell_exec('cd ' . $this->repo_dir . ' && ' . $this->git_bin_path . ' rev-parse --short HEAD');
    }

    /**
     * @return $this
     */
    private function mailProcess()
    {
        if (count($this->email)) {
            $email = array_shift($this->email);
            $this->_mailer->send(
                'Commit : ' . $this->getCommitHash(),
                'Deployment on ' . $this->web_root_dir,
                $email,
                $email,
                array_combine($this->email, $this->email)
            );
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function cleanPayload()
    {
        if ($this->_helper->checkCurrentPayload()) {
            Mage::getConfig()->deleteConfig(MageXtrem_Deployment_Helper_Data::PAYLOAD_XML_PATH);
            Mage::getConfig()->cleanCache();
        }
        
        return $this;
    }

}