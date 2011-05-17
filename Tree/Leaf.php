<?php

namespace Thekwasti\WikiBundle\Tree;

class Leaf implements NodeInterface
{
    public function __construct()
    {
    }
    
    public function getChildren()
    {
        return array();
    }
}
