<?php
class Test_Arms_Mangler_DoorkeeperCase extends PHPUnit_Framework_TestCase
{
    /**	
     * @large
     */
    public function testSimpleCommand()
    {
        $dk = Pd_Make::name('\Botion\Arms\Mangler_Doorkeeper');
        $dk->gotoLogin();
        $dk->login();
    }
}