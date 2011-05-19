<?php

namespace Thekwasti\WikiBundle;

use Thekwasti\WikiBundle\Tree\NoWiki;
use Thekwasti\WikiBundle\Tree\Link;
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
        $tokens = $this->lexer->lex($markup);
        
        $doc = new Document();
        $stack = new Stack();
        $stack->push(array($doc, 'root', null));
        
        for ($i = 0; $i < count($tokens); $i++) {
            $current = $stack->peek();
            
            $node = $current[0];
            $state = $current[1];
            $closer = is_array($current[2]) ? $current[2] : array($current[2]);
            
            $token = $tokens[$i];
            $type = $token['type'];
            $value = $token['value'];
            
            if (in_array($type, $closer)) {
                $stack->pop();
            }
            
            else if ($state == 'noparse') {
                $node->addChild(new Text($value));
            }
            
            else if ($state == 'linkdestination') {
                if ($type == Lexer::T_LINK_DELIM) {
                    $stack->pop();
                    $stack->push(array($node, 'inline_begin', $closer));
                } else {
                    $node->setDestination($node->getDestination() . $value);
                }
            }
            
            else if ($type == Lexer::T_EMPTYLINE) {
                $node->addChild(new EmptyLine());
            }
            
            else if ($state == 'root' && $type >= Lexer::T_HEADLINE_1 && $type <= Lexer::T_HEADLINE_6) {
                $headline = new Headline($type - Lexer::T_HEADLINE_1 + 1);
                $node->addChild($headline);
                $stack->push(array($headline, 'inline_begin', array(Lexer::T_NEWLINE, Lexer::T_EMPTYLINE)));
            }
            
            else if ($state == 'root' && $type == Lexer::T_HORIZONTAL_RULE) {
                $node->addChild(new HorizontalRule());
            }
            
            else if ($state == 'root' && $type >= Lexer::T_LIST_BULLET_ITEM_1 && $type <= Lexer::T_LIST_BULLET_ITEM_3) {
                $listBulletItem = new ListBulletItem($type - Lexer::T_LIST_BULLET_ITEM_1 + 1);
                $node->addChild($listBulletItem);
                $stack->push(array($listBulletItem, 'inline_begin', array(Lexer::T_NEWLINE, Lexer::T_EMPTYLINE)));
            }
            
            else if ($state == 'root' && $type >= Lexer::T_LIST_SHARP_ITEM_1 && $type <= Lexer::T_LIST_SHARP_ITEM_3) {
                $listSharpItem = new ListSharpItem($type - Lexer::T_LIST_SHARP_ITEM_1 + 1);
                $node->addChild($listSharpItem);
                $stack->push(array($listSharpItem, 'inline_begin', array(Lexer::T_NEWLINE, Lexer::T_EMPTYLINE)));
            }
            
            else if ($type == Lexer::T_BOLD) {
                $bold = new Bold();
                $node->addChild($bold);
                $stack->push(array($bold, 'inline_begin', Lexer::T_BOLD));
            }
            
            else if ($type == Lexer::T_ITALIC) {
                $italic = new Italic();
                $node->addChild($italic);
                $stack->push(array($italic, 'inline_begin', Lexer::T_ITALIC));
            }
            
            else if ($type == Lexer::T_NOWIKI_OPEN) {
                $nowiki = new NoWiki();
                $node->addChild($nowiki);
                $stack->push(array($nowiki, 'noparse', Lexer::T_NOWIKI_CLOSE));
            }
            
            else if ($type == Lexer::T_LINK_OPEN) {
                $link = new Link('');
                $node->addChild($link);
                $stack->push(array($link, 'linkdestination', Lexer::T_LINK_CLOSE));
            }
            
            else if ($type == Lexer::T_NEWLINE) {
                $node->addChild(new Text(' '));
            }
            
            else {
                if ($state == 'inline_begin') {
                    $node->addChild(new Text(ltrim($value)));
                    $stack->pop();
                    $stack->push(array($node, 'inline', $closer));
                } else {
                    $node->addChild(new Text($value));
                }
            }
        }
        
        //$this->postProcess($doc);
        return $doc;
    }
    
    private function postProcess(NodeInterface $element)
    {
        $i = 0;
        $children = $element->getChildren();
        
        if (count($children) > 0) {
            while ($i < count($children)) {
                if ($i < count($children) - 1 && $children[$i] instanceof Text && $children[$i + 1] instanceof Text) {
                    $children[$i] = new Text($children[$i]->getText() . $children[$i + 1]->getText());
                    unset($children[$i+1]);
                    $children = array_values($children);
                } else {
                    $this->postProcess($children[$i]);
                    $i++;
                }
            }
            
            $element->setChildren($children);
        }
    }
}

