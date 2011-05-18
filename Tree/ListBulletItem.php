<?php

namespace Thekwasti\WikiBundle\Tree;

class ListBulletItem extends Node
{
    private $level;
    
    public function __construct($level = 1, $children = array())
    {
        $this->level = $level;
        
        parent::__construct($children);
    }
    
    public function setLevel($level)
    {
        $this->level = $level;
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
