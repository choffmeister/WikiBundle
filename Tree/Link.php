<?php

namespace Thekwasti\WikiBundle\Tree;

class Link extends Node
{
    private $destination = '';
    private $hasSpecialPresentation = false;
    
    public function __construct($destination = '', $children = array(), $hasSpecialPresentation = false)
    {
        $this->setDestination($destination);
        $this->setHasSpecialPresentation($hasSpecialPresentation);
        
        parent::__construct($children);
    }
    
    public function setDestination($destination)
    {
        $this->destination = trim($destination);
    }
    
    public function getDestination()
    {
        return $this->destination;
    }
    
    public function setHasSpecialPresentation($hasSpecialPresentation)
    {
        $this->hasSpecialPresentation = $hasSpecialPresentation;
    }
    
    public function getHasSpecialPresentation()
    {
        return $this->hasSpecialPresentation;
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->destination,
            $this->hasSpecialPresentation,
            parent::serialize()
        ));
    }
    
    public function unserialize($serialized)
    {
        list($this->destination, $this->hasSpecialPresentation, $parent) = unserialize($serialized);
        parent::unserialize($parent);
    }
}
