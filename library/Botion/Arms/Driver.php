<?php
namespace Botion\Arms;

class Driver extends \SeleniumClient\WebDriver
{
    /**
     * @var \Zend_Config
     * @PdInject config
     */
    public $config;
    
    public function getFromBase($url)
    {
        return $this->get($this->config->urls->baseurl . '/' . $url);
    }
    
    /**
     * @param \SeleniumClient\By $locator
     * @return \SeleniumClient\WebElement
     */
    public function findSlowElement(\SeleniumClient\By $locator)
    {
        return Breaker::_()->wrap($this->findElement($locator));
    }
}