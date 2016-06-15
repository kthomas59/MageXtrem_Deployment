# MageXtrem_Deployment

`MageXtrem_Deployment` is a module for Magento 1.x providing git deployment via webhook.

This module depends of `MageXtrem_Utils` module which is included, see [here](https://github.com/kthomas59/MageXtrem_Utils).

First you'll need to setup SSH keys and clone your repository on your remote server (follow this [tutorial](http://jonathannicol.com/blog/2013/11/19/automated-git-deployments-from-bitbucket/).

Warning, don't forget to fill correct pathes (repository directory, web root directory and git binary path) and branch name under System > Configuration > MageXtrem > Development > Deployment.

A type is defined in configuration to decide if the checkout should be done via controller or cron.

Also log file is located at `<magento_root>/var/log/magextrem/deployment.log` and email addresses can be set in configuration for alerting.