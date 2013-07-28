<?php
namespace Botion\Imperion;

class Planet
{
    public $id;
    public $name;
    public $type;
    
    public function __construct($id, $name, $type = 'UNKNOWN') {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }
}