<?php
namespace Botion\Arms;
use Botion\Imperion\Building;

use \SeleniumClient\By;

class Mangler_Housewife extends Mangler_Abstract
{   
    /**
     * @PdInject new:\Botion\Arms\UrlComposer
     * @var UrlComposer
     */
    public $urlComposer;
    public $sideState = 'buildings';
    
    /**
     * checks if the currently opened page is ok
     * @return boolean
     */
    public function validatePlanet()
    {
        try {
            $element = $this->driver->findElement(By::id('Imperion-Template-PlanetSurfaceTrigger'));
            if (is_object($element)) {
                $errors = $this->driver->findElements(By::cssSelector('.Imperion-Interface-EventBox.error'));
                if (!empty($errors)) {
                    return false;
                }
            }
        } catch (\SeleniumClient\Http\SeleniumNoSuchElementException $e) {
            return false;
        }
        return is_object($element);
    }
    
    public function refreshCurrent()
    {
        $this->driver->getFromBase($this->config->urls->index);
    }
    
    /**
     * @return UiData
     */
    public function getUiData()
    {
        $sourceParser = new SourceParser($this->driver->pageSource());
        return $sourceParser->getUiData();
    }
    
    /**
     * @return SurfaceData
     */
    public function getSurfaceData()
    {
        $sourceParser = new SourceParser($this->driver->pageSource());
        return $sourceParser->getSurfaceData();
    }
    
    public function switchToPlanet(\Botion\Imperion\Planet $p)
    {
        $this->driver->get($this->urlComposer->planet($p));
        $this->sideState = ($v = $this->validatePlanet())? 'buildings' : 'invalid';
        return $v;
    }
    
    public function switchToBuildingQueue()
    {
        $e = $this->driver->findSlowElement(By::cssSelector('a.buildings'));
        $e->click();
        $this->sideState = 'ships';
        return $this->validatePlanet();
    }
    
    public function switchToShipQueue()
    {
        $e = $this->driver->findSlowElement(By::cssSelector('a.ships'));
        $e->click();
        $this->sideState = 'buildings';
        return $this->validatePlanet();
    }
    
    public function switchToBuilding(Building $building)
    {
        $triggers = $this->driver->findElements(By::cssSelector('.Imperion-Interface-PlanetSurfaceListTrigger'));
        if (isset($triggers[$i = $building->triggerId])) {
            $e = Breaker::_()->wrap($triggers[$i]);
            $e->click();
            $this->sideState = 'building';
            return $this->validatePlanet();
        } else {
            return false;
        }
    }
    
    /**
     * @param Building $building
     * @return boolean
     */
    public function updatePossible(Building $building)
    {
        $elements = $this->driver->findElements(By::cssSelector('.Imperion-Interface-Gui-Button.upgradeBot.disabled'));
        if (!empty($elements)) return false;
        $elements = $this->driver->findElements(By::cssSelector('.Imperion-Interface-Gui-Button.upgradeBot.plus'));
        if (!empty($elements)) return false;
        $elements = $this->driver->findElements(By::cssSelector('.Imperion-Interface-Gui-Button.upgradeBot'));
        return !empty($elements);
    }
    
    public function updateBuilding(Building $building)
    {
        try {
            $element = $this->driver->findSlowElement(By::cssSelector('.Imperion-Interface-Gui-Button.upgradeBot'));
            $element->click();
            return true;
        } catch (\SeleniumClient\Http\SeleniumNoSuchElementException $e) {
            return false;
        }
    }
    
    /**
     * @return \ArrayObject
     */
    public function getCurrentQueue()
    {
        $elements = $this->driver->findElements(By::cssSelector('.Imperion-Interface-BuildQueue ul.list li.entry'));
        $queue = new \ArrayObject();
        foreach ($elements as $element) {
            $queue->append(new \Botion\Imperion\QueueItemBuilding());
        }
        return $queue; 
    }
}
