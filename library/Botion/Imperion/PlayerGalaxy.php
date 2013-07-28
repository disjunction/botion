<?php
namespace Botion\Imperion;

class PlayerGalaxy extends \ArrayObject
{
    const CENTAURUS = 1;
    const PHOENIX = 2;
    const FORNAX = 3;
    
    public $id;
    public $name;
    
    public function getPlanets()
    {
        return $this->getArrayCopy();
    }
}