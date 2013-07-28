<?php
namespace Botion\Arms;

class Mangler_Abstract
{
    /**
    * @PdInject driver
    * @var Driver
    */
    public $driver;
    
    /**
     * @PdInject config
     * @var Zend_Config
     */
    public $config;
}