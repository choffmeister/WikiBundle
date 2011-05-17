<?php

namespace Thekwasti\WikiBundle\Tree;

class Headline implements NodeInterface
{
    private $content;
    private $level;
    
    public function __construct(NodeInterface $content, $level = 1)
    {
        $this->content = $content;
        $this->level = $level;
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function getLevel()
    {
        return $this->level;
    }
}
