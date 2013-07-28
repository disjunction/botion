<?php
namespace Botion\Arms;

class Breaker
{
    /**
     * @PdInject config
     * @var Zend_Config
     */
    public $config;
    
    /**
     * @var \SeleniumClient\WebElement
     */
    protected $_element;
    
    /**
     * the returned value is actually, but we deceive the IDE ;P
     * 
     * @param \SeleniumClient\WebElement $element
     * @return \SeleniumClient\WebElement
     */
    public function wrap($element)
    {
        if (!$this->config->breaker->enabled) return $element;
        $me = \Pd_Make::name(__CLASS__);
        $me->_element = $element;
        return $me;
    }
        
    public function randomSleep($name) {
        usleep(rand(floor($this->config->breaker->get($name) / 2), $this->config->breaker->get($name)) * 1000);
    }

    public function fixedSleep($name) {
        usleep($this->config->breaker->get($name) * 1000);
    }
    
    
    public function __call($name, $arguments)
    {
        $this->randomSleep('unknown');
        return call_user_func(array($this->_element, $name), $arguments);
    }
    
    public function click()
    {
        $this->randomSleep('click');
        $this->_element->click();
        $this->fixedSleep('afterClick');
    }
    
    public function sendKeys($text)
    {
        $letters = str_split($text);
        foreach ($letters as $letter) {
            $this->_element->sendKeys($letter);
            $this->randomSleep('typing');
        }
    }
    
    /**
     * @return self
     */
    public function _()
    {
        return \Pd_Container::get()->dependencies()->get('breaker');
    }
}