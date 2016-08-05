<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 13/06/16
 * Time: 11:04
 */
class MageXtrem_Deployment_DeployController extends Mage_Core_Controller_Front_Action
{

    public function pushAction()
    {
        try {
            Mage::getModel('magextrem_deployment/api_v2')->push();
        } catch (Exception $e) {
            Mage::helper('magextrem_deployment/logger')->logException($e);
        }
    }

}