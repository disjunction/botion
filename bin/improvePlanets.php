<?php
namespace Botion\Arms;

require 'bootstrap.php';

class Bin_ImprovePlanets extends BinAbstract
{
    public function run()
    {
        global $argv;
        
        $worker = \Pd_Make::name('\Botion\Brain\PlanetImprover');
        
        if (count($argv) > 1) {
            array_shift($argv);
            $worker->setPlanetIds($argv);
        }
        
        $worker->run();
    }
}

Bin_ImprovePlanets::runStatic();