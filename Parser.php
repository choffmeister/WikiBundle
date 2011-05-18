<?php

namespace Thekwasti\WikiBundle;

use Thekwasti\WikiBundle\Tree\HorizontalRule;
use Thekwasti\WikiBundle\Tree\Bold;
use Thekwasti\WikiBundle\Tree\Italic;
use Thekwasti\WikiBundle\Tree\ListSharpItem;
use Thekwasti\WikiBundle\Tree\ListBulletItem;
use Thekwasti\WikiBundle\Tree\Headline;
use Thekwasti\WikiBundle\Tree\Text;
use Thekwasti\WikiBundle\Tree\EmptyLine;
use Thekwasti\WikiBundle\Tree\Document;
use Thekwasti\WikiBundle\Tree\NodeInterface;

class Parser
{
    private $lexer;
    private $tokens;
    private $i;
    
    public function __construct()
    {
        $this->lexer = new Lexer();
    }
    
    public function parse($markup)
    {
        $this->i = 0;
        $this->tokens = $this->lexer->lex($markup);
        return $this->recursion('Document');
    }
    
    private function recursion($current, $closeToken = null)
    {
        $children = array();
        
        while ($this->i < count($this->tokens)) {
            $token = $this->tokens[$this->i];
            $type = $token['type'];
            $value = $token['value'];
            
            if ($closeToken == $token['type']) {
                $this->i++;
                break;
            }
            
            if ($type == Lexer::T_NEWLINE) {
                $this->i++;
                $children[] = new EmptyLine();
            }
            
            else if ($current == 'Document' && $type == Lexer::T_HEADLINE) {
                $this->i++;
                $children[] = new Headline(1, $this->recursion('', Lexer::T_NEWLINE));
            }
            
            else if ($current == 'Document' && $type == Lexer::T_HORIZONTAL_RULE) {
                $this->i++;
                $children[] = new HorizontalRule();
            }
            
            else if ($current == 'Document' && $type == Lexer::T_LIST_BULLET_ITEM) {
                $this->i++;
                $children[] = new ListBulletItem(1, $this->recursion('', Lexer::T_NEWLINE));
            }
            
            else if ($current == 'Document' && $type == Lexer::T_LIST_SHARP_ITEM) {
                $this->i++;
                $children[] = new ListSharpItem(1, $this->recursion('', Lexer::T_NEWLINE));
            }
            
            else if ($type == Lexer::T_BOLD) {
                $this->i++;
                $children[] = new Bold($this->recursion('', Lexer::T_BOLD));
            }
            
            else if ($type == Lexer::T_ITALIC) {
                $this->i++;
                $children[] = new Italic($this->recursion('', Lexer::T_ITALIC));
            }
            
            else {
                $this->i++;
                $children[] = new Text($value);
            }
        }
        
        if ($current === '') {
            return $children;
        }
        
        $class = '\\Thekwasti\\WikiBundle\\Tree\\' . $current;
        return new $class($children);
    }
}

