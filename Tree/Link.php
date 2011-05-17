<?php

namespace Thekwasti\WikiBundle\Tree;

class Link implements NodeInterface
{
    private $destination;
    private $content;
    
    public function __construct($destination, NodeInterface $content = null)
    {
        $this->destination = trim($destination);
        $this->content = $content;
    }
    
    public function getDestination()
    {
        return $this->destination;
    }
    
    public function getContent()
    {
        return $this->content ?: new Text($this->destination);
    }
}
