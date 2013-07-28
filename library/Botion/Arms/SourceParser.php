<?php
namespace Botion\Arms;

class SourceParser
{
    public $source;
    
    /**
     * @var UiData
     */
    protected $_uiData;
    
    /**
    * @var SurfaceData
    */
    protected $_surfaceData;
    
    public function __construct($source)
    {
        $this->source = $source;
    }
    
    /**
     * @return NULL|UiData
     */
    public function getUiData()
    {
        if ($this->_uiData) return $this->_uiData;
        
        $r = preg_match("/var uiData = JSON.decode\('(.+)'/Us", $this->source, $matches);
        if (!$r) return null;
        $data = json_decode($matches[1], $assoc = true);
        //file_put_contents('data/ui_data_array.serialized', serialize($data));
        return $this->_uiData = new UiData($data);
    }
    
    public function getSurfaceData()
    {
        if ($this->_surfaceData) return $this->_surfaceData;
    
        $r = preg_match("/var surfaceData = JSON.decode\('(.+)'/Us", $this->source, $matches);
        if (!$r) return null;
        $data = json_decode($matches[1], $assoc = true);
        return $this->_surfaceData = new SurfaceData($data);
    }
}