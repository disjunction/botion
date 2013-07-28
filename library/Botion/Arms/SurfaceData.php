<?php
namespace Botion\Arms;

use Botion\Imperion\Building;

class SurfaceData
{
    public $data;
    protected $_buildings;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function getBuildings()
    {
        if (isset($this->_buildings)) return $this->_buildings;
        $this->_buildings = array();
        foreach ($this->data['buildings'] as $data) {
            $b = new Building();
            $b->futureLevel = $data['future_level'];
            $b->level = $data['level'];
            $b->triggerId = $data['nr_ground'];
            $b->type = $data['type'];
            $b->caption = $data['caption'];
            
            $this->_buildings[] = $b;
        }
        return $this->_buildings;
    }
}