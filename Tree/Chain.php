<?php

namespace Thekwasti\WikiBundle\Tree;

class Chain implements NodeInterface
{
    private $elements = array();
    
    public function __construct(array $elements = array())
    {
        foreach ($elements as $element) {
            if (!$element instanceof NodeInterface) {
                throw new \InvalidArgumentException('$$elements must be an array of NodeInterface objects');
            }
        }
        
        $this->elements = $elements;
    }
    
    public function addElement(NodeInterface $element)
    {
        $this->elements[] = $element;
    }
    
    public function getElements()
    {
        return $this->elements;
    }
}
