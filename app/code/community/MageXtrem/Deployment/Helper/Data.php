<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 09/06/16
 * Time: 14:21
 */
class MageXtrem_Deployment_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Config paths
     */
    const REPO_DIR_XML_PATH = 'magextrem_utils/deployment/repo_dir';
    const WEB_ROOT_DIR_XML_PATH = 'magextrem_utils/deployment/web_root_dir';
    const GIT_BIN_PATH_XML_PATH = 'magextrem_utils/deployment/git_bin_path';
    const PAYLOAD_XML_PATH = 'magextrem_utils/deployment/payload';
    const PUSH_TYPE_XML_PATH = 'magextrem_utils/deployment/push_type';
    const ALLOWED_IPS_XML_PATH = 'magextrem_utils/deployment/allowed_ips';
    const BRANCH_NAME_XML_PATH = 'magextrem_utils/deployment/branch_name';
    const EMAIL_XML_PATH = 'magextrem_utils/deployment/email';

    /**
     * @return stdClass
     * @throws Zend_Json_Exception
     */
    public function getPayload()
    {
        return Zend_Json::decode(Mage::getStoreConfig(self::PAYLOAD_XML_PATH, Zend_Json::TYPE_OBJECT));
    }

    /**
     * @return stdClass
     * @throws Zend_Json_Exception
     */
    public function getInputPayload()
    {
        return Zend_Json::decode(file_get_contents('php://input'), Zend_Json::TYPE_OBJECT);
    }

    /**
     * @return bool
     */
    public function checkCurrentPayload()
    {
        $payload = Mage::getStoreConfig(self::PAYLOAD_XML_PATH);
        return !empty($payload);
    }

    /**
     * @return string
     */
    public function getPushType() {
        return Mage::getStoreConfig(self::PUSH_TYPE_XML_PATH);
    }

    /**
     * @return bool
     */
    public function checkRemoteAddress()
    {
        $ip = Mage::helper('magextrem_utils/env')->getIP();
        $ip_long = ip2long($ip);
        $allowed_ips = explode(',', Mage::getStoreConfig(self::ALLOWED_IPS_XML_PATH));
        $allowed = false;
        $i = 0;
        while (!$allowed && ($i < count($allowed_ips))) {
            $allowed_ip = trim($allowed_ips[$i]);
            if (strpos($allowed_ip, '/') !== false) {
                list ($subnet, $bits) = explode('/', $allowed_ip);
            } else {
                $subnet = $allowed_ip;
                $bits = false;
            }

            // Simple IP
            if (!$bits) {
                if ($allowed_ip == $ip) {
                    return true;
                }
            } // IP Range
            else {
                $subnet = ip2long($subnet);
                $mask = -1 << (32 - $bits);
                $subnet &= $mask;
                if (($ip_long & $mask) == $subnet) {
                    return true;
                }
            }
            $i++;
        }
        return false;
    }

}