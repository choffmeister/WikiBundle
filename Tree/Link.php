<?php

namespace Thekwasti\WikiBundle\Tree;

class Link extends Node
{
    private $destination;
    
    public function __construct($destination, $children = array())
    {
        $this->destination = trim($destination);
        
        parent::__construct($children);
    }
    
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }
    
    public function getDestination()
    {
        return $this->destination;
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->destination,
            parent::serialize()
        ));
    }
    
    public function unserialize($serialized)
    {
        list($this->destination, $parent) = unserialize($serialized);
        parent::unserialize($parent);
    }
}
