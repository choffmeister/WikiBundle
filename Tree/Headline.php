<?php

namespace Thekwasti\WikiBundle\Tree;

class Headline extends Leaf
{
    private $level;
    private $text;
    
    public function __construct($level = 1, $text = '')
    {
        $this->level = $level;
        $this->text = $text;
    }
    
    public function setLevel($level)
    {
        $this->level = $level;
    }
    
    public function getLevel()
    {
        return $this->level;
    }
    
    public function setText($text)
    {
        $this->text = $text;
    }
    
    public function getText()
    {
        return $this->text;
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->level,
            $this->text,
        ));
    }
    
    public function unserialize($serialized)
    {
        list($this->level, $this->text) = unserialize($serialized);
    }
}
