<?php

namespace Thekwasti\WikiBundle\Tree;

class Text extends Leaf
{
    private $text;
    
    public function __construct($text = '')
    {
        $this->text = $text;
        
        parent::__construct();
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
            $this->text,
            parent::serialize()
        ));
    }
    
    public function unserialize($serialized)
    {
        list($this->text, $parent) = unserialize($serialized);
        parent::unserialize($parent);
    }
}
