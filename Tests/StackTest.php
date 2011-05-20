<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\Stack;

class StackTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $o1 = new \stdClass();
        $o2 = new \stdClass();
        $o3 = new \stdClass();
        
        $stack = new Stack();
        
        $this->assertEquals(0, $stack->count());
        $stack->push($o1);
        $this->assertEquals(1, $stack->count());
        $stack->push($o2);
        $this->assertEquals(2, $stack->count());
        $stack->push($o3);
        $this->assertEquals(3, $stack->count());
        $stack->push($o3);
        $this->assertEquals(4, $stack->count());
        
        $this->assertSame($o3, $stack->peek());
        $this->assertEquals(4, $stack->count());
        $this->assertSame($o3, $stack->peek());
        $this->assertEquals(4, $stack->count());
        
        $array = array();
        foreach ($stack as $element)
            $array[] = $element;
        $this->assertEquals(array($o1, $o2, $o3, $o3), $array);
        
        $this->assertSame($o3, $stack->pop());
        $this->assertEquals(3, $stack->count());
     
        $this->assertSame($o3, $stack->pop());
        $this->assertEquals(2, $stack->count());
        
        $this->assertSame($o2, $stack->pop());
        $this->assertEquals(1, $stack->count());
        
        $this->assertSame($o1, $stack->pop());
        $this->assertEquals(0, $stack->count());
        
        try {
            $stack->pop();
            $this->fail('->pop should throw an Exception');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('LogicException', $ex, '->pop should throw a LogicException');
        }
        
        try {
            $stack->peek();
            $this->fail('->peek should throw an Exception');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('LogicException', $ex, '->peek should throw a LogicException');
        }
    }
}