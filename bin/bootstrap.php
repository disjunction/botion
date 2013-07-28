<?php
!defined('APPLICATION_ENV') and define('APPLICATION_ENV', 'development');
define('APPLICATION_PATH', realpath(__DIR__ . '/..'));

set_include_path(get_include_path() . PATH_SEPARATOR .
    implode(PATH_SEPARATOR, array(
        APPLICATION_PATH . '/library',
    ))
);

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()
    ->registerNamespace('SeleniumClient')
    ->registerNamespace('Botion')
    ->registerNamespace('Pd');

$deps = Pd_Container::get()->dependencies()
    ->set('config', new Zend_Config_Xml(APPLICATION_PATH . '/configs/config.xml', APPLICATION_ENV))
    ->set('breaker', Pd_Make::name('\Botion\Arms\Breaker'));

$driverFactory = Pd_Make::name('\Botion\Arms\DriverFactory');
$deps->set('driver', $driverFactory->getPersistent());
