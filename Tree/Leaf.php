<?php

namespace Thekwasti\WikiBundle\Tree;

class Leaf implements NodeInterface
{
    public function __construct()
    {
    }
    
    public function addChild(NodeInterface $child)
    {
        throw new \LogicException();
    }
    
    public function getChildren()
    {
        return array();
    }
    
    public function setChildren($children)
    {
        throw new \LogicException();
    }
    
    public function serialize()
    {
        return '';
    }
    
    public function unserialize($serialized)
    {
    }
}
