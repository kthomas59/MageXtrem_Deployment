# MageXtrem_Deployment

`MageXtrem_Deployment` is a module for Magento 1.x providing git deployment via webhook.

This module depends of `MageXtrem_Utils` module which is included, see [here](https://github.com/kthomas59/MageXtrem_Utils).

A type is defined in configuration to decide if the checkout should be done via controller or cron.

Log file is located at `<magento_root>/var/log/magextrem/deployment.log` and email addresses can be set in configuration for alerting.