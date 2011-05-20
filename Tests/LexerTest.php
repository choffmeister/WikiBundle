<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\Lexer;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    public function testLexWithNoString()
    {
        $lexer = new Lexer();
        
        try {
            $lexer->lex(new \DateTime());
            $this->fail('->lex should throw an Exception');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('InvalidArgumentException', $ex, '->lex should throw an InvalidArgumentException');
        }
    }
    
    public function testGetLiteral()
    {
        $lexer = new Lexer();
        $this->assertEquals('T_NEWLINE', $lexer->getLiteral(Lexer::T_NEWLINE));
        $this->assertEquals('T_PIPE', $lexer->getLiteral(Lexer::T_PIPE));
        
        try {
            $lexer->getLiteral(-1);
            $this->fail('->getLiteral should throw an Exception');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('InvalidArgumentException', $ex, '->getLiteral should throw an InvalidArgumentException');
        }
    }
}