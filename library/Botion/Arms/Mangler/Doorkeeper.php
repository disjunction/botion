<?php
namespace Botion\Arms;
use \SeleniumClient\By;

class Mangler_Doorkeeper extends Mangler_Abstract
{   
    public function gotoIndex()
    {
        $this->driver->getFromBase($this->config->urls->index);
    }
    
    public function gotoLogin()
    {
        $this->driver->getFromBase($this->config->urls->login);
    }
    
    public function login()
    {
        $e = $this->driver->findSlowElement(By::cssSelector('.Portal-GUI-Button'));
        $e->click();
        usleep(500);
        
        $e = $this->driver->findElement(By::cssSelector('iframe'));
        $this->driver->switchTo()->getFrameByWebElement($e);
        
        
        $e = $this->driver->findSlowElement(By::name('email'));
        $e->clear();
        $e->sendKeys($this->config->auth->login);
        
        $e = $this->driver->findSlowElement(By::name('password'));
        $e->clear();
        $e->sendKeys($this->config->auth->pass);
        
        $e = $this->driver->findSlowElement(By::id('submit'));
        $e->click();
    }
}