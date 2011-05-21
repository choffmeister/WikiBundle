<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\Tree\Node;
use Thekwasti\WikiBundle\Tree\Leaf;
use Thekwasti\WikiBundle\Tree\TableCellHead;
use Thekwasti\WikiBundle\Tree\TableCell;
use Thekwasti\WikiBundle\Tree\TableRow;
use Thekwasti\WikiBundle\Tree\Table;
use Thekwasti\WikiBundle\Tree\NoWikiInline;
use Thekwasti\WikiBundle\Tree\ListItem;
use Thekwasti\WikiBundle\Tree\OrderedList;
use Thekwasti\WikiBundle\Tree\UnorderedList;
use Thekwasti\WikiBundle\Tree\Paragraph;
use Thekwasti\WikiBundle\Tree\NoWiki;
use Thekwasti\WikiBundle\Tree\ListSharpItem;
use Thekwasti\WikiBundle\Tree\ListBulletItem;
use Thekwasti\WikiBundle\Tree\Document;
use Thekwasti\WikiBundle\Tree\HorizontalRule;
use Thekwasti\WikiBundle\Tree\Link;
use Thekwasti\WikiBundle\Tree\Italic;
use Thekwasti\WikiBundle\Tree\Bold;
use Thekwasti\WikiBundle\Tree\NodeInterface;
use Thekwasti\WikiBundle\Tree\Headline;
use Thekwasti\WikiBundle\Tree\Chain;
use Thekwasti\WikiBundle\Tree\Text;
use Thekwasti\WikiBundle\Tree\Breakline;

class NodeTests extends \PHPUnit_Framework_TestCase
{
    public function testLeaf()
    {
        $leaf = new Leaf();
        
        try {
            $leaf->addChild(new Node());
            $this->fail('->addChild should throw an Exception');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('LogicException', $ex, '->addChild should throw a LogicException');
        }
        
        try {
            $leaf->setChildren(new Node());
            $this->fail('->setChildren should throw an Exception');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('LogicException', $ex, '->setChildren should throw a LogicException');
        }
        
        $copy = new Leaf();
        $copy->unserialize($leaf->serialize());
        $this->assertEquals($leaf, $copy);
    }
    
    public function testNode()
    {
        $o1 = new Node();
        $o2 = new Node();

        $node = new Node();
        $this->assertEquals(array(), $node->getChildren());
        $node->setChildren(array($o1));
        $this->assertSame(array($o1), $node->getChildren());
        $node->addChild($o2);
        $this->assertSame(array($o1, $o2), $node->getChildren());
        
        try {
            $node->setChildren(array($o1, $o2, new \stdClass()));
            $this->fail('->addChild should throw an Exception');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('InvalidArgumentException', $ex, '->addChild should throw an InvalidArgumentException');
        }
        
        $this->assertSame(array($o1, $o2), $node->getChildren());
        
        $node2 = new Node($o1);
        $this->assertSame(array($o1), $node2->getChildren());

        $node3 = new Node(array($o1, $o2));
        $this->assertSame(array($o1, $o2), $node3->getChildren());
        
        $copy = new Node();
        $copy->unserialize($node->serialize());
        $this->assertEquals($node, $copy);
    }
    
    public function testText()
    {
        $text = new Text('123');
        $this->assertEquals('123', $text->getText());
        $text->setText('foo');
        $this->assertEquals('foo', $text->getText());
        
        $copy = new Text();
        $copy->unserialize($text->serialize());
        $this->assertEquals($text, $copy);
    }
    
    public function testHeadline()
    {
        $headline = new Headline(1, '123');
        $this->assertEquals('123', $headline->getText());
        $headline->setText('foo');
        $this->assertEquals('foo', $headline->getText());
        $this->assertEquals(1, $headline->getLevel());
        $headline->setLevel(3);
        $this->assertEquals(3, $headline->getLevel());
        
        $copy = new Headline();
        $copy->unserialize($headline->serialize());
        $this->assertEquals($headline, $copy);
    }
    
    public function testLink()
    {
        $text = new Text('123');
        $link = new Link('foo', $text, true);
        $this->assertEquals('foo', $link->getDestination());
        $link->setDestination('foo2');
        $this->assertEquals('foo2', $link->getDestination());
        $this->assertEquals(true, $link->getHasSpecialPresentation());
        $link->setHasSpecialPresentation(false);
        $this->assertEquals(false, $link->getHasSpecialPresentation());
        
        $copy = new Link();
        $copy->unserialize($link->serialize());
        $this->assertEquals($link, $copy);
        
        $link->setHasSpecialPresentation(true);
        $this->assertEquals(true, $link->getHasSpecialPresentation());
        
        $copy = new Link();
        $copy->unserialize($link->serialize());
        $this->assertEquals($link, $copy);
    }
    
    public function testOrderedList()
    {
        $t1 = new Text('1a');
        $t2 = new Text('2a');
        $l1 = new ListItem($t1);
        $l2 = new ListItem($t2);
        
        $orderedList = new OrderedList(3, array($l1, $l2));
        $this->assertEquals(3, $orderedList->getLevel());
        $orderedList->setLevel(2);
        $this->assertEquals(2, $orderedList->getLevel());
        
        $copy = new OrderedList();
        $copy->unserialize($orderedList->serialize());
        $this->assertEquals($orderedList, $copy);
    }
    
    public function testUnunorderedList()
    {
        $t1 = new Text('1a');
        $t2 = new Text('2a');
        $l1 = new ListItem($t1);
        $l2 = new ListItem($t2);
        
        $unorderedList = new UnorderedList(3, array($l1, $l2));
        $this->assertEquals(3, $unorderedList->getLevel());
        $unorderedList->setLevel(2);
        $this->assertEquals(2, $unorderedList->getLevel());
        
        $copy = new UnorderedList();
        $copy->unserialize($unorderedList->serialize());
        $this->assertEquals($unorderedList, $copy);
    }
    
    public function testSubclassCopies()
    {
        $bold = new Bold();
        $breakline = new Breakline();
        $document = new Document();
        $horizontalRule = new HorizontalRule();
        $italic = new Italic();
        $listItem = new ListItem();
        $noWiki = new NoWiki();
        $noWikiInline = new NoWikiInline();
        $paragraph = new Paragraph();
        $table = new Table();
        $tableCell = new TableCell();
        $tableCellHead = new TableCellHead();
        $tableRow = new TableRow();
    }
}