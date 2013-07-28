<?php
namespace Botion\Brain;

use Botion\Arms\UiData;
use Botion\Arms\SurfaceData;
use Botion\Imperion\Building;
use Botion\Imperion\Planet;
use Botion\Imperion\PlayerGalaxy;

/**
 * automatically Improves all Planets if improvements are available
 * emulating to some extend the human behaviour
 * 
 * The steps of the improvements are pronounced via reporter
 */
class PlanetImprover
{
    /**
     * @PdInject new:\Botion\Arms\Mangler_Housewife
     * @var \Botion\Arms\Mangler_Housewife
     */
    public $hw;

    /**
    * @PdInject config
    * @var \Zend_Config
    */
    public $config;

    /**
     * @PdInject new:\Botion\Arms\Reporter
     * @var \Botion\Arms\Reporter
     */
    public $reporter;

    /**
    * @PdInject new:\Botion\Arms\Breaker
    * @var \Botion\Arms\Breaker
    */
    public $breaker;

    /**
     * @var array Planet[]
     */
    protected $_planned;

    /**
     * @var array
     */
    protected $_planetIds;

    public function setPlanetIds(array $ids)
    {
        $this->_planetIds = $ids;
    }

    /**
     * do all phases of improvement
     *
     * interal Botion exceptions are reported, unexpected - thrown
     *
     * @throws \Exception
     */
    public function run()
    {
        try {
            $this->_prepare();
            $this->_makePlan();
            $this->_executePlan();
            $this->reporter->say('Iteration finished');
        } catch (Exception $e) {
            // Botion\Brain\Exception is expected and we're fine with that
            $this->reporter->say("exception " . $e->getMessage());
        }
    }

    /**
     * Make sure the WebDriver is in the correct state 
     * and it's possible to start planet improvements
     * @throws Exception
     */
    protected function _prepare()
    {
        $this->reporter->say('Starting iteration.');

        $this->hw->refreshCurrent();

        // if not on a planet, relogin and check again
        if (!$this->hw->validatePlanet()) {
            $this->reporter->say('We are not on a planet, trying to log in again.');
            $dk = \Pd_Make::name('\Botion\Arms\Mangler_Doorkeeper');
            $dk->gotoLogin();
            $dk->login();
            if (!$this->hw->validatePlanet()) {
                throw new Exception('Login failed.');
            }
        }
    }

    /**
     * prepare a list of planets for improving
     *
     * result is stored in $this->_planned
     */
    protected function _makePlan()
    {
        $uiData = $this->hw->getUiData();
        $galaxies = $uiData->getPlayerGalaxies();

        // complete list of planets in both galaxies
        $planets = array_merge($galaxies[PlayerGalaxy::FORNAX]->getPlanets(),
                $galaxies[PlayerGalaxy::CENTAURUS]->getPlanets());

        $this->_planned = array();

        // if the planets are predefined
        if (isset($this->_planetIds)) {
            foreach ($this->_planetIds as $id) {
                foreach ($planets as $planet) {
                    $planet->id == $id and $this->_planned[] = $planet;
                }
            }
        } else {
            // .. else pick random number of random planets (max 3 of them)
            $plannedCount = rand(min(3, count($planets)));

            while (count($this->_planned) < $plannedCount) {
                $portion = array_splice($planets, rand(0, count($planets) - 1), 1);
                $this->_planned[] = $portion[0];
            }
        }
        $this->reporter->say(count($this->_planned) . ' planets are planned for improving');
    }

    /**
     * do improvements on all planets planned for improvement
     */
    protected function _executePlan()
    {
        $i=0;
        $b = array_values($this->config->planetImprover->planetBlacklist->toArray());
        $blackListedIds = is_array($b[0])? array_values($b[0]) : array($b[0]);

        foreach ($this->_planned as $planet) {
            $i++;
            if (in_array($planet->id, $blackListedIds)) {
                $this->reporter->say('skipping blacklisted planet ' . $planet->name);
                continue;
            }

            try {
                $this->reporter->say('improving ' . $planet->name . '. Planet ' . $i . ' of ' . count($this->_planned));
                $this->_improveOne($planet);
            } catch (\Exception $e) {
                $this->reporter->say('exception in planet loop. Trying to bypass to the next one');

                // reporter should not pronounce it
                echo $e, "\n";

                // refresh state after inconsistency, just in case
                $this->hw->refreshCurrent();
            }
            $this->breaker->randomSleep('planetFinished');
        }
    }

    /**
     * Do all possible improvements on given planet
     * @param Planet $planet
     * @return void
     */
    protected function _improveOne(Planet $planet)
    {
        if (!$this->hw->switchToPlanet($planet)) {
            $this->reporter->say('failed to switch to ' . $planet->name);
            return;
        }

        $this->breaker->randomSleep('afterPlanetSwitch');

        if (!($queue = $this->hw->getCurrentQueue())) {
            $this->reporter->say('failed to parse building queue on ' . $planet->name);
            return;
        }

        if (($len = count($queue)) > 2) {
            $this->reporter->say('queue is full on ' . $planet->name);
            return;
        }

        $sd = $this->hw->getSurfaceData();
        if (!$sd) {
            $this->reporter->say('failed to get surface data on ' . $planet->name);
            return;
        }

        $uiData = $this->hw->getUiData();
        if (!$uiData) {
            $this->reporter->say('failed to get surface data on ' . $planet->name);
            return;
        }

        $tasksDone = 0;
        while ($this->_tryAddingToTheQueue($planet, $sd, $uiData)) {
            $tasksDone++;

            if ($tasksDone == 5) {
                $this->reporter->say('refreshing after 5 successful upgrades on ' . $planet->name);
                $this->hw->refreshCurrent();
                if ($this->hw->getCurrentQueue()->count() >= 3) {
                    $this->reporter->say('The queue was full actually. Switching to the next planet.');
                    return;
                }
            }

            if ($tasksDone > 10) {
                $this->reporter->say('endless loop suspected on ' . $planet->name);
                return;
            }
        }
    }

    /**
     * finds the most suitable building for improvement and tries adding it to the queue
     *
     * returns true if we should continue adding more buildings
     *
     * @param Planet $planet
     * @param SurfaceData $sd
     * @param UiData $uiData
     * @return boolean
     */
    protected function _tryAddingToTheQueue(Planet $planet, SurfaceData $sd, UiData $uiData)
    {
        $buildings = $sd->getBuildings();

        // sort buildings by their futureLevel
        usort($buildings, function($b1, $b2) {
            return $b1->futureLevel - $b2->futureLevel;
        });

        foreach ($buildings as $building) {
            // skip those which are good enough
            if ($building->futureLevel >= $this->_getLimit($building)) continue;

            $result = $this->_tryAddingBuilding($building, $planet);
            if ($result) {
                // we should continue unless the queue has already 3 or more items
                return $this->hw->getCurrentQueue()->count() < 3;
            } else if (!$this->hw->validatePlanet()) {
                $this->reporter->say('state is inconsistent on ' . $planet->name);
                return false;
            }
        }

        return false;
    }

    /**
     * Adds given building to the queue.
     * 
     * Can fail for several reasons according to game logic (no exceptions needed)
     * 
     * @param Building $building
     * @param Planet $planet
     * @return boolean
     */
    protected function _tryAddingBuilding(Building $building, Planet $planet)
    {
        if (!$this->hw->switchToBuilding($building)) {
            $this->reporter->say('switch to ' . $building->caption . ' failed.');
            return false;
        }

        if (!$this->hw->updatePossible($building)) {
            $this->reporter->say('upgrading ' . $building->caption . ' impossible.');
            return false;
        }

        $this->reporter->say('upgrading ' . $building->caption);

        if ($this->hw->updateBuilding($building)) {
            $building->futureLevel++;
            return true;
        } else {
            $this->reporter->say('upgrade failed.');
            return false;
        }
    }

    /**
     * max level of building we WANT to improve it to
     * @param Building $building
     * @return int
     */
    protected function _getLimit(Building $building)
    {
        if (strlen($limit = $this->config->planetImprover->buildingLimits->get($building->type))) {
            return $limit;
        }
        return $this->config->planetImprover->buildingLimits->default;
    }
}
