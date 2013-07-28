<?php
namespace Botion\Arms;

class UrlComposer
{
    /**
     * @PdInject config
     * @var Zend_Config
     */
    public $config;
    
    public function planet(\Botion\Imperion\Planet $p)
    {
        return $this->config->urls->baseurl . '/planet/index/change/planetId/' . $p->id;
    }
}