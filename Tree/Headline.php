<?php

namespace Thekwasti\WikiBundle\Tree;

class Headline extends Node
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
}
