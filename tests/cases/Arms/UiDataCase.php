<?php
class Test_Arms_UiDataCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Botion\Arms\UiData
     */
    public $uiData;
    
    public function setUp()
    {
        $data = unserialize(file_get_contents('data/ui_data_array.serialized'));
        $this->uiData = new \Botion\Arms\UiData($data);
    }
    
    public function testGetPlayerGalaxies()
    {
        $pGalaxies = $this->uiData->getPlayerGalaxies();
        $this->assertNotEmpty($pGalaxies[1]);
        $this->assertNotEmpty($pGalaxies[1]->name);
        $this->assertNotEmpty($pGalaxies[1]->id);
        $this->assertNotEmpty($pGalaxies[3][0]);
    }
}