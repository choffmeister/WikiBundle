<?php

/*
 * This file is part of WikiBundle
 *
 * (c) Christian Hoffmeister <choffmeister.github@googlemail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Thekwasti\WikiBundle;

/**
 * Stack
 * 
 * @author Christian Hoffmeister <choffmeister.github@googlemail.com>
 */
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
