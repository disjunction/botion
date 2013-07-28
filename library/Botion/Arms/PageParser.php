<?php
namespace Botion\Arms;

class PageParser
{
    public $client;
    
    public function __construct(Driver $client)
    {
        $this->client = $client;
    }
    
}