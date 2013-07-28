<?php
namespace Botion\Arms;

class UiData
{
    public $data;
    
    protected $_pGalaxies;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function getPlayerGalaxies()
    {
        if ($this->_pGalaxies) return $this->_pGalaxies;
        
        foreach ($this->data['planets'] as $key => $galaxyData) {
            $pGalaxy = new \Botion\Imperion\PlayerGalaxy();
            $pGalaxy->name = $galaxyData['name'];
            $pGalaxy->id = $key;
            foreach ($galaxyData['planets'] as $planetData) {
                $planet = new \Botion\Imperion\Planet($planetData['id_planet'],
                                                      $planetData['name'],
                                                      $planetData['type']);
                $pGalaxy[] = $planet;
            }
            
            $this->_pGalaxies[$key] = $pGalaxy;
        }
        
        return $this->_pGalaxies;
    }
}