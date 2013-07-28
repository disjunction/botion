<?php
namespace Botion\Arms;

use Botion\Imperion\Building;
use Botion\Imperion\PlayerGalaxy;

require 'bootstrap.php';

class Bin_Manipulate extends BinAbstract
{
    
    public function run()
    {
        \Pd_Container::get()->dependencies()
        ->set('some', function(){return new stdClass();});
        $building = \Pd_Make::name('\Botion\Imperion\Building');
        var_dump($building);
        
    }
    
    public function runOld()
    {
        /* @var $hw \Botion\Arms\Mangler_Housewife */
        /* @var $dk \Botion\Arms\Mangler_Doorkeeper */

        $hw = \Pd_Make::name('\Botion\Arms\Mangler_Housewife');
        if (!$hw->validatePlanet()) {
            $hw->refreshCurrent();
            if (!$hw->validatePlanet()) { 
                $dk = \Pd_Make::name('\Botion\Arms\Mangler_Doorkeeper');
                $dk->gotoLogin();
                $dk->login();
            }
        }
        
        $locator = \Pd_Make::name('\Botion\Arms\SurfaceLocator');
        $locator->locateByTriggerId(1);
    }
}

Bin_Manipulate::runStatic();