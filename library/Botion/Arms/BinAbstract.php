<?php
namespace Botion\Arms;

abstract class BinAbstract
{
    public static function runStatic() {
        $o = new static();
        $o->run();
    }
}