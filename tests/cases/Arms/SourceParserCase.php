<?php
class Test_Arms_SourceParserCase extends PHPUnit_Framework_TestCase
{
    public function testGettingUiData()
    {
        $sp = new \Botion\Arms\SourceParser(file_get_contents('../snippets/p3.html'));
        $this->assertInstanceOf('\Botion\Arms\UiData', $sp->getUiData());
    }
}