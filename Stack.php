<?php

namespace Thekwasti\WikiBundle;

class Stack implements \Countable, \IteratorAggregate
{
    private $stack = array();
    private $count = 0;
    
    public function push($object)
    {
        array_push($this->stack, $object);
        $this->count++;
    }
    
    public function pop()
    {
        if ($this->count == 0) {
            throw new \LogicException();
        }
        
        $this->count--;
        return array_pop($this->stack);
    }
    
    public function peek()
    {
        if ($this->count == 0) {
            throw new \LogicException();
        }
        
        return end($this->stack);
    }
    
    public function has(\Closure $callback)
    {
        foreach ($this->stack as $element) {
            if ($callback($element)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function count()
    {
        return $this->count;
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->stack);
    }
}
