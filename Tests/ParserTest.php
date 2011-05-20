<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\Parser;
use Thekwasti\WikiBundle\Tree\TableRow;
use Thekwasti\WikiBundle\Tree\TableCellHead;
use Thekwasti\WikiBundle\Tree\TableCell;
use Thekwasti\WikiBundle\Tree\Table;
use Thekwasti\WikiBundle\Tree\NoWikiInline;
use Thekwasti\WikiBundle\Tree\ListItem;
use Thekwasti\WikiBundle\Tree\OrderedList;
use Thekwasti\WikiBundle\Tree\UnorderedList;
use Thekwasti\WikiBundle\Tree\Paragraph;
use Thekwasti\WikiBundle\Tree\NoWiki;
use Thekwasti\WikiBundle\Tree\Link;
use Thekwasti\WikiBundle\Tree\HorizontalRule;
use Thekwasti\WikiBundle\Tree\Bold;
use Thekwasti\WikiBundle\Tree\Italic;
use Thekwasti\WikiBundle\Tree\Headline;
use Thekwasti\WikiBundle\Tree\Text;
use Thekwasti\WikiBundle\Tree\EmptyLine;
use Thekwasti\WikiBundle\Tree\Document;
use Thekwasti\WikiBundle\Tree\NodeInterface;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParagraph()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("foo");
        $this->assertEquals(new Document(new Paragraph(array(new Text('foo')))), $doc); 

        $doc = $parser->parse("\nfoo");
        $this->assertEquals(new Document(new Paragraph(array(new Text('foo')))), $doc); 
        
        $doc = $parser->parse("\n\nfoo");
        $this->assertEquals(new Document(new Paragraph(array(new Text('foo')))), $doc); 
        
        $doc = $parser->parse("foo\nfoo2");
        $this->assertEquals(new Document(new Paragraph(array(new Text('foo'), new Text(' '), new Text('foo2')))), $doc); 
        
        $doc = $parser->parse("foo\n\nfoo2");
        $this->assertEquals(new Document(array(new Paragraph(new Text('foo')), new Paragraph(new Text('foo2')))), $doc); 
    }
    
    public function testHeadline()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("= Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Headline(1, new Text(' Headline')), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("\n= Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Headline(1, new Text(' Headline')), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("\n\n= Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Headline(1, new Text(' Headline')), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("=== Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Headline(3, new Text(' Headline')), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("d=== Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Paragraph(new Text('d=== Headline')), new Paragraph(new Text('Foo')))), $doc);
    }
    
    public function testBold()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("Hello **fold**");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Bold(new Text('fold'))))), $doc);
        
        $doc = $parser->parse("Hello **fold** asd");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Bold(new Text('fold')), new Text(' asd')))), $doc);
        
        $doc = $parser->parse("Hello **fold\nasd**");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Bold(array(new Text('fold'), new Text(' '), new Text('asd')))))), $doc);
        
        $doc = $parser->parse("Hello **fold\n\nasd**");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('Hello '), new Bold(new Text('fold')))), new Paragraph(array(new Text('asd'), new Bold())))), $doc);
    }
    
    public function testItalic()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("Hello //fold//");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Italic(new Text('fold'))))), $doc);
        
        $doc = $parser->parse("Hello //fold// asd");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Italic(new Text('fold')), new Text(' asd')))), $doc);
        
        $doc = $parser->parse("Hello //fold\nasd//");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Italic(array(new Text('fold'), new Text(' '), new Text('asd')))))), $doc);
        
        $doc = $parser->parse("Hello //fold\n\nasd//");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('Hello '), new Italic(new Text('fold')))), new Paragraph(array(new Text('asd'), new Italic())))), $doc);
    }
    
    public function testUnorderedList()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("* First\n*Second\n*Third");
        $this->assertEquals(new Document(new UnorderedList(1, array(new ListItem(array(new Text(' First'), new Text(' '))), new ListItem(array(new Text('Second'), new Text(' '))), new ListItem(array(new Text('Third')))))), $doc);
        
        $doc = $parser->parse("asdsad\n* First\n*Second\n*Third");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('asdsad'), new Text(' '))), new UnorderedList(1, array(new ListItem(array(new Text(' First'), new Text(' '))), new ListItem(array(new Text('Second'), new Text(' '))), new ListItem(array(new Text('Third'))))))), $doc);
        
        $doc = $parser->parse("* First\n*Second\n*Third\n\nasdsad");
        $this->assertEquals(new Document(array(new UnorderedList(1, array(new ListItem(array(new Text(' First'), new Text(' '))), new ListItem(array(new Text('Second'), new Text(' '))), new ListItem(array(new Text('Third'))))), new Paragraph(new Text('asdsad')))), $doc);
        
        $doc = $parser->parse("* First\n*Second\nSecondB\n*Third");
        $this->assertEquals(new Document(new UnorderedList(1, array(new ListItem(array(new Text(' First'), new Text(' '))), new ListItem(array(new Text('Second'), new Text(' '), new Text('SecondB'), new Text(' '))), new ListItem(new Text('Third'))))), $doc);

        $doc = $parser->parse("*1\n*2\n**2a\n**2b\n*3");
        $this->assertEquals(new Document(
            new UnorderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new UnorderedList(2, array(
                    new ListItem(array(new Text('2a'), new Text(' '))),
                    new ListItem(array(new Text('2b'), new Text(' '))),
                )),
                new ListItem(array(new Text('3')))
            ))
        ), $doc);
        
        $doc = $parser->parse("*1\n*2\n**2a\n**2b\n***Deep\n*3");
        $this->assertEquals(new Document(
            new UnorderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new UnorderedList(2, array(
                    new ListItem(array(new Text('2a'), new Text(' '))),
                    new ListItem(array(new Text('2b'), new Text(' '))),
                    new UnorderedList(3, array(
                        new ListItem(array(new Text('Deep'), new Text(' '))),
                    )),
                )),
                new ListItem(array(new Text('3')))
            ))
        ), $doc);
        
        $doc = $parser->parse("*1\n*2\n**2a\n**2b\n***Deep\n**2c");
        $this->assertEquals(new Document(
            new UnorderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new UnorderedList(2, array(
                    new ListItem(array(new Text('2a'), new Text(' '))),
                    new ListItem(array(new Text('2b'), new Text(' '))),
                    new UnorderedList(3, array(
                        new ListItem(array(new Text('Deep'), new Text(' '))),
                    )),
                    new ListItem(array(new Text('2c'))),
                )),
            ))
        ), $doc);
    }
    
    public function testOrderedList()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("# First\n#Second\n#Third");
        $this->assertEquals(new Document(new OrderedList(1, array(new ListItem(array(new Text(' First'), new Text(' '))), new ListItem(array(new Text('Second'), new Text(' '))), new ListItem(array(new Text('Third')))))), $doc);
        
        $doc = $parser->parse("asdsad\n# First\n#Second\n#Third");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('asdsad'), new Text(' '))), new OrderedList(1, array(new ListItem(array(new Text(' First'), new Text(' '))), new ListItem(array(new Text('Second'), new Text(' '))), new ListItem(array(new Text('Third'))))))), $doc);
        
        $doc = $parser->parse("# First\n#Second\n#Third\n\nasdsad");
        $this->assertEquals(new Document(array(new OrderedList(1, array(new ListItem(array(new Text(' First'), new Text(' '))), new ListItem(array(new Text('Second'), new Text(' '))), new ListItem(array(new Text('Third'))))), new Paragraph(new Text('asdsad')))), $doc);
        
        $doc = $parser->parse("# First\n#Second\nSecondB\n#Third");
        $this->assertEquals(new Document(new OrderedList(1, array(new ListItem(array(new Text(' First'), new Text(' '))), new ListItem(array(new Text('Second'), new Text(' '), new Text('SecondB'), new Text(' '))), new ListItem(new Text('Third'))))), $doc);

        $doc = $parser->parse("#1\n#2\n##2a\n##2b\n#3");
        $this->assertEquals(new Document(
            new OrderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new OrderedList(2, array(
                    new ListItem(array(new Text('2a'), new Text(' '))),
                    new ListItem(array(new Text('2b'), new Text(' '))),
                )),
                new ListItem(array(new Text('3')))
            ))
        ), $doc);
        
        $doc = $parser->parse("#1\n#2\n##2a\n##2b\n###Deep\n#3");
        $this->assertEquals(new Document(
            new OrderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new OrderedList(2, array(
                    new ListItem(array(new Text('2a'), new Text(' '))),
                    new ListItem(array(new Text('2b'), new Text(' '))),
                    new OrderedList(3, array(
                        new ListItem(array(new Text('Deep'), new Text(' '))),
                    )),
                )),
                new ListItem(array(new Text('3')))
            ))
        ), $doc);
        
        $doc = $parser->parse("#1\n#2\n##2a\n##2b\n###Deep\n##2c");
        $this->assertEquals(new Document(
            new OrderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new OrderedList(2, array(
                    new ListItem(array(new Text('2a'), new Text(' '))),
                    new ListItem(array(new Text('2b'), new Text(' '))),
                    new OrderedList(3, array(
                        new ListItem(array(new Text('Deep'), new Text(' '))),
                    )),
                    new ListItem(array(new Text('2c'))),
                )),
            ))
        ), $doc);
    }
    
    public function testUnorderedOrderedListInterlaced()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("*1\n*2\n#3\n#4\n*5");
        $this->assertEquals(new Document(array(
            new UnorderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new OrderedList(1, array(
                    new ListItem(array(new Text('3'), new Text(' '))),
                    new ListItem(array(new Text('4'), new Text(' '))),
                )),
                new ListItem(array(new Text('5'))),
            )),
        )), $doc);
        
        $doc = $parser->parse("*1\n*2\n#3\n#4\n##5\n##6\n**7\n***8\n##9");
        $this->assertEquals(new Document(array(
            new UnorderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new OrderedList(1, array(
                    new ListItem(array(new Text('3'), new Text(' '))),
                    new ListItem(array(new Text('4'), new Text(' '))),
                    new OrderedList(2, array(
                        new ListItem(array(new Text('5'), new Text(' '))),
                        new ListItem(array(new Text('6'), new Text(' '))),
                        new UnorderedList(2, array(
                            new ListItem(array(new Text('7'), new Text(' '))),
                            new UnorderedList(3, array(
                                new ListItem(array(new Text('8'), new Text(' '))),
                            )),
                        )),
                        new ListItem(array(new Text('9'))),
                    )),
                )),
            )),
        )), $doc);
        
        $doc = $parser->parse("#1\n#2\n*3\n*4\n#5");
        $this->assertEquals(new Document(array(
            new OrderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new UnorderedList(1, array(
                    new ListItem(array(new Text('3'), new Text(' '))),
                    new ListItem(array(new Text('4'), new Text(' '))),
                )),
                new ListItem(array(new Text('5'))),
            )),
        )), $doc);
        
        $doc = $parser->parse("#1\n#2\n*3\n*4\n**5\n**6\n##7\n###8\n**9");
        $this->assertEquals(new Document(array(
            new OrderedList(1, array(
                new ListItem(array(new Text('1'), new Text(' '))),
                new ListItem(array(new Text('2'), new Text(' '))),
                new UnorderedList(1, array(
                    new ListItem(array(new Text('3'), new Text(' '))),
                    new ListItem(array(new Text('4'), new Text(' '))),
                    new UnorderedList(2, array(
                        new ListItem(array(new Text('5'), new Text(' '))),
                        new ListItem(array(new Text('6'), new Text(' '))),
                        new OrderedList(2, array(
                            new ListItem(array(new Text('7'), new Text(' '))),
                            new OrderedList(3, array(
                                new ListItem(array(new Text('8'), new Text(' '))),
                            )),
                        )),
                        new ListItem(array(new Text('9'))),
                    )),
                )),
            )),
        )), $doc);
    }
}