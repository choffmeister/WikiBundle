<?php

namespace Thekwasti\WikiBundle\Tree;

class Link extends Node
{
    private $destination;
    
    public function __construct($destination, $children)
    {
        $this->destination = trim($destination);
        
        parent::__construct($children);
    }
    
    public function getDestination()
    {
        return $this->destination;
    }
}
