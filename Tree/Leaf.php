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
    
    public function serialize()
    {
        return '';
    }
    
    public function unserialize($serialized)
    {
    }
}
