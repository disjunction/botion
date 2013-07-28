<?php
namespace Botion\Arms;

class SurfaceLocator
{
    /**
     * @var @PdInject driver
     * @var Driver
     */
    public $driver;
    
    /**
     * @param int $triggerId
     * @return \SeleniumClient\WebElement
     */
    public function locateByTriggerId($triggerId)
    {
        $elements = $this->driver->findElements(\SeleniumClient\By::cssSelector('.Imperion-Interface-PlanetSurfaceListTrigger.taken'));

        foreach ($elements as $element) {
            $style = $element->getAttribute('style');
            $parts = explode(';', $style);
            
            $coordinates = array();
            
            foreach ($parts as $pos) {
                $keyVal = explode(':', $pos);
                if (count($keyVal) != 2) continue;
                $coordinates[trim($keyVal[0])] = $keyVal[1];
            }
            
            var_dump($coordinates);
        }
    }
}