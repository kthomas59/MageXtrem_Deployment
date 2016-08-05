<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 13/06/16
 * Time: 16:05
 */
class MageXtrem_Deployment_Model_Source_Push_Type extends MageXtrem_Utils_Model_Source_Abstract
{

    const DIRECT_PUSH_TYPE = 'direct';
    const CRON_PUSH_TYPE = 'cron';

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            self::DIRECT_PUSH_TYPE => Mage::helper('magextrem_deployment')->__('Direct'),
            self::CRON_PUSH_TYPE => Mage::helper('magextrem_deployment')->__('Cron')
        );
    }

}