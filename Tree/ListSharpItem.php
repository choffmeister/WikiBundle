<?php

namespace Thekwasti\WikiBundle\Tree;

class ListSharpItem extends Node
{
    private $level;
    
    public function __construct($level = 1, $children)
    {
        $this->level = $level;
        
        parent::__construct($children);
    }
    
    public function getLevel()
    {
        return $this->level;
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->level,
            parent::serialize()
        ));
    }
    
    public function unserialize($serialized)
    {
        list($this->level, $parent) = unserialize($serialized);
        parent::unserialize($parent);
    }
}
