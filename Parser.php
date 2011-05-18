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
    
    public function __construct()
    {
        $this->lexer = new Lexer();
    }
    
    public function parse($markup)
    {
        $i = 0;
        $tokens = $this->lexer->lex($markup);
        return $this->recursion('Document', $tokens, $i);
    }
    
    private function recursion($current, $tokens, &$i, $delimiter = null)
    {
        if ($current == 'Inline') {
            $children = array();
            
            while ($i < count($tokens)) {
                $token = $tokens[$i];
                
                if ($delimiter !== null && $delimiter == $token['type']) {
                    $i++;
                    break;
                }
                
                switch ($token['type']) {
                    case Lexer::T_BOLD:
                        $i++;
                        $children[] = new Bold($this->recursion('Inline', $tokens, $i, Lexer::T_BOLD));
                        break;
                    case Lexer::T_ITALIC:
                        $i++;
                        $children[] = new Italic($this->recursion('Inline', $tokens, $i, Lexer::T_ITALIC));
                        break;
                    default:
                        $i++;
                        $children[] = new Text($token['value']);
                        break;
                }
            }
            
            return $children;
        } else if ($current == 'Document') {
            $children = array();
            
            while ($i < count($tokens)) {
                $token = $tokens[$i];
                
                switch ($token['type']) {
                    case Lexer::T_NEWLINE:
                        $i++;
                        $children[] = new EmptyLine();
                        break;
                    case Lexer::T_HEADLINE:
                        $i++;
                        $children[] = new Headline(1, $this->recursion('Inline', $tokens, $i, Lexer::T_NEWLINE));
                        break;
                    case Lexer::T_HORIZONTAL_RULE:
                        $i++;
                        $children[] = new HorizontalRule();
                        break;
                    case Lexer::T_LIST_BULLET_ITEM:
                        $i++;
                        $children[] = new ListBulletItem(1, $this->recursion('Inline', $tokens, $i, Lexer::T_NEWLINE));
                        break;
                    case Lexer::T_LIST_SHARP_ITEM:
                        $i++;
                        $children[] = new ListSharpItem(1, $this->recursion('Inline', $tokens, $i, Lexer::T_NEWLINE));
                        break;
                    default:
                        $i++;
                        $children[] = new Text($token['value']);
                        break;
                }
            }
            
            return new Document($children);
        }
    }
}
