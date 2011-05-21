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
use Thekwasti\WikiBundle\Tree\Document;
use Thekwasti\WikiBundle\Tree\NodeInterface;
use Thekwasti\WikiBundle\Tree\Breakline;

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

        $doc = $parser->parse("\n\n\n\n\nfoo");
        $this->assertEquals(new Document(new Paragraph(array(new Text('foo')))), $doc); 
        
        $doc = $parser->parse("foo\n");
        $this->assertEquals(new Document(new Paragraph(array(new Text('foo ')))), $doc); 
        
        $doc = $parser->parse("foo\n\n");
        $this->assertEquals(new Document(new Paragraph(array(new Text('foo ')))), $doc); 

        $doc = $parser->parse("foo\n\n\n\n\n");
        $this->assertEquals(new Document(new Paragraph(array(new Text('foo ')))), $doc); 
        
        $doc = $parser->parse("foo\nfoo2");
        $this->assertEquals(new Document(new Paragraph(array(new Text('foo foo2')))), $doc); 
        
        $doc = $parser->parse("foo\n\nfoo2");
        $this->assertEquals(new Document(array(new Paragraph(new Text('foo')), new Paragraph(new Text('foo2')))), $doc); 
        
        $doc = $parser->parse("foo\n\n\n\n\nfoo2");
        $this->assertEquals(new Document(array(new Paragraph(new Text('foo')), new Paragraph(new Text('foo2')))), $doc); 
        
        $doc = $parser->parse("foo\n*A");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('foo '))), new UnorderedList(1, new ListItem(new Text('A'))))), $doc); 

        $doc = $parser->parse("foo\n##A");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('foo '))), new OrderedList(2, new ListItem(new Text('A'))))), $doc);
        
        $doc = $parser->parse("foo\n|=A");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('foo '))), new Table(new TableRow(new TableCellHead(new Text('A')))))), $doc); 

        $doc = $parser->parse("foo\n{{{\nA");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('foo '))), new NoWiki(array(new Text("\nA"))))), $doc); 
    }
    
    public function testHeadline()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("= Headline");
        $this->assertEquals(new Document(new Headline(1, 'Headline')), $doc);
        
        $doc = $parser->parse("= Headline ");
        $this->assertEquals(new Document(new Headline(1, 'Headline')), $doc);
        
        $doc = $parser->parse("= Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Headline(1, 'Headline'), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("\n=Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Headline(1, 'Headline'), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("\n\n= Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Headline(1, 'Headline'), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("=== Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Headline(3, 'Headline'), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("d=== Headline\n\nFoo");
        $this->assertEquals(new Document(array(new Paragraph(new Text('d=== Headline')), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("= Headline\nFoo");
        $this->assertEquals(new Document(array(new Headline(1, 'Headline'), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("\n=Headline\nFoo");
        $this->assertEquals(new Document(array(new Headline(1, 'Headline'), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("\n\n= Headline\nFoo");
        $this->assertEquals(new Document(array(new Headline(1, 'Headline'), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("=== Headline\nFoo");
        $this->assertEquals(new Document(array(new Headline(3, 'Headline'), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("d=== Headline\nFoo");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('d=== Headline Foo'))))), $doc);
        
        $doc = $parser->parse("=== Headline=");
        $this->assertEquals(new Document(array(new Headline(3, 'Headline'))), $doc);
        
        $doc = $parser->parse("=== Headline ===\nFoo");
        $this->assertEquals(new Document(array(new Headline(3, 'Headline'), new Paragraph(new Text('Foo')))), $doc);
        
        $doc = $parser->parse("=== Head=line ===\nFoo");
        $this->assertEquals(new Document(array(new Headline(3, 'Head=line'), new Paragraph(new Text('Foo')))), $doc);
    }
    
    public function testHeadlineBoldInterlaced()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("= Hello //**fo");
        $this->assertEquals(new Document(new Headline(1, 'Hello //**fo')), $doc);
        
        $doc = $parser->parse("== Hello //**fold**//\nTest");
        $this->assertEquals(new Document(array(new Headline(2, 'Hello //**fold**//'), new Paragraph(new Text('Test')))), $doc);
        
        $doc = $parser->parse("=== Hello //**fold\nTest");
        $this->assertEquals(new Document(array(new Headline(3, 'Hello //**fold'), new Paragraph(new Text('Test')))), $doc);

        $doc = $parser->parse("== Hello //**fold\n\nTest");
        $this->assertEquals(new Document(array(new Headline(2, 'Hello //**fold'), new Paragraph(new Text('Test')))), $doc);
    }
    
    public function testBold()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("Hello **fold**");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Bold(new Text('fold'))))), $doc);
        
        $doc = $parser->parse("Hello **fold** asd");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Bold(new Text('fold')), new Text(' asd')))), $doc);
        
        $doc = $parser->parse("Hello **fold\nasd**");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Bold(array(new Text('fold asd')))))), $doc);
        
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
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Italic(array(new Text('fold asd')))))), $doc);
        
        $doc = $parser->parse("Hello //fold\n\nasd//");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('Hello '), new Italic(new Text('fold')))), new Paragraph(array(new Text('asd'), new Italic())))), $doc);
    }
    
    public function testBoldItalicInterlaced()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("Hello //**fold**//");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Italic(new Bold(new Text('fold')))))), $doc);
        
        $doc = $parser->parse("Hello **//fold//**");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Bold(new Italic(new Text('fold')))))), $doc);
        
        $doc = $parser->parse("Hello //**fold// No");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Italic(new Bold(new Text('fold'))), new Text(' No')))), $doc);
        
        $doc = $parser->parse("Hello **//fold** No");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello '), new Bold(new Italic(new Text('fold'))), new Text(' No')))), $doc);
    
        $doc = $parser->parse("0**1//2**3**4//5**6");
        $this->assertEquals(new Document(new Paragraph(array(
            new Text('0'),
            new Bold(array(
                new Text('1'),
                new Italic(array(
                    new Text('2'),
                )),
            )),
            new Text('3'),
            new Bold(array(
                new Text('4'),
                new Italic(array(
                    new Text('5'),
                )),
            )),
            new Text('6'),
        ))), $doc);
        
        $doc = $parser->parse("0//1**2//3//4**5//6");
        $this->assertEquals(new Document(new Paragraph(array(
            new Text('0'),
            new Italic(array(
                new Text('1'),
                new Bold(array(
                    new Text('2'),
                )),
            )),
            new Text('3'),
            new Italic(array(
                new Text('4'),
                new Bold(array(
                    new Text('5'),
                )),
            )),
            new Text('6'),
        ))), $doc);
    }
    
    public function testUnorderedList()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("* First\n*Second\n*Third");
        $this->assertEquals(new Document(new UnorderedList(1, array(new ListItem(array(new Text(' First '))), new ListItem(array(new Text('Second '))), new ListItem(array(new Text('Third')))))), $doc);
        
        $doc = $parser->parse("asdsad\n* First\n*Second\n*Third");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('asdsad '))), new UnorderedList(1, array(new ListItem(array(new Text(' First '))), new ListItem(array(new Text('Second '))), new ListItem(array(new Text('Third'))))))), $doc);
        
        $doc = $parser->parse("* First\n*Second\n*Third\n\nasdsad");
        $this->assertEquals(new Document(array(new UnorderedList(1, array(new ListItem(array(new Text(' First '))), new ListItem(array(new Text('Second '))), new ListItem(array(new Text('Third'))))), new Paragraph(new Text('asdsad')))), $doc);
        
        $doc = $parser->parse("* First\n*Second\nSecondB\n*Third");
        $this->assertEquals(new Document(new UnorderedList(1, array(new ListItem(array(new Text(' First '))), new ListItem(array(new Text('Second SecondB '))), new ListItem(new Text('Third'))))), $doc);

        $doc = $parser->parse("*1\n*2\n**2a\n**2b\n*3");
        $this->assertEquals(new Document(
            new UnorderedList(1, array(
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new UnorderedList(2, array(
                    new ListItem(array(new Text('2a '))),
                    new ListItem(array(new Text('2b '))),
                )),
                new ListItem(array(new Text('3')))
            ))
        ), $doc);
        
        $doc = $parser->parse("*1\n*2\n**2a\n**2b\n***Deep\n*3");
        $this->assertEquals(new Document(
            new UnorderedList(1, array(
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new UnorderedList(2, array(
                    new ListItem(array(new Text('2a '))),
                    new ListItem(array(new Text('2b '))),
                    new UnorderedList(3, array(
                        new ListItem(array(new Text('Deep '))),
                    )),
                )),
                new ListItem(array(new Text('3')))
            ))
        ), $doc);
        
        $doc = $parser->parse("*1\n*2\n**2a\n**2b\n***Deep\n**2c");
        $this->assertEquals(new Document(
            new UnorderedList(1, array(
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new UnorderedList(2, array(
                    new ListItem(array(new Text('2a '))),
                    new ListItem(array(new Text('2b '))),
                    new UnorderedList(3, array(
                        new ListItem(array(new Text('Deep '))),
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
        $this->assertEquals(new Document(new OrderedList(1, array(new ListItem(array(new Text(' First '))), new ListItem(array(new Text('Second '))), new ListItem(array(new Text('Third')))))), $doc);
        
        $doc = $parser->parse("asdsad\n# First\n#Second\n#Third");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('asdsad '))), new OrderedList(1, array(new ListItem(array(new Text(' First '))), new ListItem(array(new Text('Second '))), new ListItem(array(new Text('Third'))))))), $doc);
        
        $doc = $parser->parse("# First\n#Second\n#Third\n\nasdsad");
        $this->assertEquals(new Document(array(new OrderedList(1, array(new ListItem(array(new Text(' First '))), new ListItem(array(new Text('Second '))), new ListItem(array(new Text('Third'))))), new Paragraph(new Text('asdsad')))), $doc);
        
        $doc = $parser->parse("# First\n#Second\nSecondB\n#Third");
        $this->assertEquals(new Document(new OrderedList(1, array(new ListItem(array(new Text(' First '))), new ListItem(array(new Text('Second SecondB '))), new ListItem(new Text('Third'))))), $doc);

        $doc = $parser->parse("#1\n#2\n##2a\n##2b\n#3");
        $this->assertEquals(new Document(
            new OrderedList(1, array(
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new OrderedList(2, array(
                    new ListItem(array(new Text('2a '))),
                    new ListItem(array(new Text('2b '))),
                )),
                new ListItem(array(new Text('3')))
            ))
        ), $doc);
        
        $doc = $parser->parse("#1\n#2\n##2a\n##2b\n###Deep\n#3");
        $this->assertEquals(new Document(
            new OrderedList(1, array(
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new OrderedList(2, array(
                    new ListItem(array(new Text('2a '))),
                    new ListItem(array(new Text('2b '))),
                    new OrderedList(3, array(
                        new ListItem(array(new Text('Deep '))),
                    )),
                )),
                new ListItem(array(new Text('3')))
            ))
        ), $doc);
        
        $doc = $parser->parse("#1\n#2\n##2a\n##2b\n###Deep\n##2c");
        $this->assertEquals(new Document(
            new OrderedList(1, array(
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new OrderedList(2, array(
                    new ListItem(array(new Text('2a '))),
                    new ListItem(array(new Text('2b '))),
                    new OrderedList(3, array(
                        new ListItem(array(new Text('Deep '))),
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
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new OrderedList(1, array(
                    new ListItem(array(new Text('3 '))),
                    new ListItem(array(new Text('4 '))),
                )),
                new ListItem(array(new Text('5'))),
            )),
        )), $doc);
        
        $doc = $parser->parse("*1\n*2\n#3\n#4\n##5\n##6\n**7\n***8\n##9");
        $this->assertEquals(new Document(array(
            new UnorderedList(1, array(
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new OrderedList(1, array(
                    new ListItem(array(new Text('3 '))),
                    new ListItem(array(new Text('4 '))),
                    new OrderedList(2, array(
                        new ListItem(array(new Text('5 '))),
                        new ListItem(array(new Text('6 '))),
                        new UnorderedList(2, array(
                            new ListItem(array(new Text('7 '))),
                            new UnorderedList(3, array(
                                new ListItem(array(new Text('8 '))),
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
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new UnorderedList(1, array(
                    new ListItem(array(new Text('3 '))),
                    new ListItem(array(new Text('4 '))),
                )),
                new ListItem(array(new Text('5'))),
            )),
        )), $doc);
        
        $doc = $parser->parse("#1\n#2\n*3\n*4\n**5\n**6\n##7\n###8\n**9");
        $this->assertEquals(new Document(array(
            new OrderedList(1, array(
                new ListItem(array(new Text('1 '))),
                new ListItem(array(new Text('2 '))),
                new UnorderedList(1, array(
                    new ListItem(array(new Text('3 '))),
                    new ListItem(array(new Text('4 '))),
                    new UnorderedList(2, array(
                        new ListItem(array(new Text('5 '))),
                        new ListItem(array(new Text('6 '))),
                        new OrderedList(2, array(
                            new ListItem(array(new Text('7 '))),
                            new OrderedList(3, array(
                                new ListItem(array(new Text('8 '))),
                            )),
                        )),
                        new ListItem(array(new Text('9'))),
                    )),
                )),
            )),
        )), $doc);
    }
    
    public function testLink()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("[[apple]]");
        $this->assertEquals(new Document(new Paragraph(new Link('apple'))), $doc);
        
        $doc = $parser->parse("[[apple|]]");
        $this->assertEquals(new Document(new Paragraph(new Link('apple', array(), true))), $doc);
        
        $doc = $parser->parse("[[apple|asd]]");
        $this->assertEquals(new Document(new Paragraph(new Link('apple', array(new Text('asd')), true))), $doc);
        
        $doc = $parser->parse("[[apple|//asd//]]");
        $this->assertEquals(new Document(new Paragraph(new Link('apple', array(new Italic(new Text('asd'))), true))), $doc);
        
        $doc = $parser->parse("[[apple|//asd]]");
        $this->assertEquals(new Document(new Paragraph(new Link('apple', array(new Italic(array(new Text('asd')))), true))), $doc);
        
        $doc = $parser->parse("pre[[apple|//asd//]]post");
        $this->assertEquals(new Document(new Paragraph(array(new Text('pre'), new Link('apple', array(new Italic(new Text('asd'))), true), new Text('post')))), $doc);
        
        $doc = $parser->parse("pre[[apple\n\npost");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('pre'), new Link('apple'))), new Paragraph(new Text('post')))), $doc);
        
        $doc = $parser->parse("pre[[apple|foo\n\npost");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('pre'), new Link('apple', array(new Text('foo')), true))), new Paragraph(new Text('post')))), $doc);

        $doc = $parser->parse("0[[1|2//3]]4");
        $this->assertEquals(new Document(new Paragraph(array(
            new Text('0'),
            new Link('1', array(
                new Text('2'),
                new Italic(
                    new Text('3')
                ),
            ), true),
            new Text('4'),
        ))), $doc);
        
        $doc = $parser->parse('Foo]]Do');
        $this->assertEquals(new Document(new Paragraph(new Text('Foo]]Do'))), $doc);
    }
    
    public function testTable()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("|=H1|= H2 \n|1a|1b \n| 2a| 2b ");
        $this->assertEquals(new Document(
            new Table(array(
                new TableRow(array(
                    new TableCellHead(new Text('H1')),
                    new TableCellHead(new Text(' H2 ')),
                )),
                new TableRow(array(
                    new TableCell(new Text('1a')),
                    new TableCell(new Text('1b ')),
                )),
                new TableRow(array(
                    new TableCell(new Text(' 2a')),
                    new TableCell(new Text(' 2b ')),
                )),
            ))
        ), $doc);
        
        $doc = $parser->parse("pre\n|=H1|= H2 \n|1a|1b \n| 2a| 2b \npost");
        $this->assertEquals(new Document(array(
            new Paragraph(array(new Text('pre '))),
            new Table(array(
                new TableRow(array(
                    new TableCellHead(new Text('H1')),
                    new TableCellHead(new Text(' H2 ')),
                )),
                new TableRow(array(
                    new TableCell(new Text('1a')),
                    new TableCell(new Text('1b ')),
                )),
                new TableRow(array(
                    new TableCell(new Text(' 2a')),
                    new TableCell(new Text(' 2b ')),
                )),
            )),
            new Paragraph(new Text('post')),
        )), $doc);
        
        $doc = $parser->parse("pre\n|=H1|= H2 \n\n|1a|1b \n| 2a| 2b \npost");
        $this->assertEquals(new Document(array(
            new Paragraph(array(new Text('pre '))),
            new Table(array(
                new TableRow(array(
                    new TableCellHead(new Text('H1')),
                    new TableCellHead(new Text(' H2 ')),
                )),
            )),
            new Table(array(
                new TableRow(array(
                    new TableCell(new Text('1a')),
                    new TableCell(new Text('1b ')),
                )),
                new TableRow(array(
                    new TableCell(new Text(' 2a')),
                    new TableCell(new Text(' 2b ')),
                )),
            )),
            new Paragraph(new Text('post')),
        )), $doc);
    }
    
    public function testNoWiki()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("{{{nowiki\n}}}");
        $this->assertEquals(new Document(new NoWiki(array(new Text("nowiki\n")))), $doc);
        
        $doc = $parser->parse("{{{**no**wiki\n}}}");
        $this->assertEquals(new Document(new NoWiki(array(new Text("**no**wiki\n")))), $doc);

        $doc = $parser->parse("Foo {{{**no**wiki}}}");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Foo '), new NoWikiInline(array(new Text('**no**wiki')))))), $doc);
        
        $doc = $parser->parse("Foo {{{**no**wiki\n}}}");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Foo '), new NoWikiInline(array(new Text('**no**wiki ')))))), $doc);

        $doc = $parser->parse("Foo {{{**no**wiki\nasd}}}");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Foo '), new NoWikiInline(array(new Text('**no**wiki asd')))))), $doc);
        
        $doc = $parser->parse("Foo\n{{{**no**wiki\n}}}");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('Foo '))), new NoWiki(array(new Text("**no**wiki\n"))))), $doc);
    }
    
    public function testHorizontalRule()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("----");
        $this->assertEquals(new Document(new HorizontalRule()), $doc);
        
        $doc = $parser->parse("pre\n----\npost");
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('pre '))), new HorizontalRule(), new Paragraph(new Text('post')))), $doc);
        
        $doc = $parser->parse("pre\n\n----\n\npost");
        
        $this->assertEquals(new Document(array(new Paragraph(array(new Text('pre'))), new HorizontalRule(), new Paragraph(new Text('post')))), $doc);
    }
    
    public function testBreakline()
    {
        $parser = new Parser();
        
        $doc = $parser->parse("1\\\\2");
        $this->assertEquals(new Document(new Paragraph(array(new Text('1'), new Breakline(), new Text('2')))), $doc);
        
        $doc = $parser->parse("\\\\2");
        $this->assertEquals(new Document(new Paragraph(array(new Breakline(), new Text('2')))), $doc);
        
        $doc = $parser->parse("1\\\\");
        $this->assertEquals(new Document(new Paragraph(array(new Text('1'), new Breakline()))), $doc);
    }
    
    public function testEscaping()
    {
        $parser = new Parser();
        
        $doc = $parser->parse('= Head');
        $this->assertEquals(new Document(new Headline(1, 'Head')), $doc);
        
        $doc = $parser->parse('~= Head');
        $this->assertEquals(new Document(new Paragraph(new Text('= Head'))), $doc);
        
        $doc = $parser->parse('==~ Head');
        $this->assertEquals(new Document(new Headline(2, '~ Head')), $doc);
        
        $doc = $parser->parse("Hello ~**fold**");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello **fold'), new Bold()))), $doc);
        
        $doc = $parser->parse("Hello ~~**fold**");
        $this->assertEquals(new Document(new Paragraph(array(new Text('Hello ~'), new Bold(new Text('fold'))))), $doc);
        
        $doc = $parser->parse("{{{nowiki\n}}}");
        $this->assertEquals(new Document(new NoWiki(array(new Text("nowiki\n")))), $doc);
        
        $doc = $parser->parse("~{{{nowiki\n}}}");
        $this->assertEquals(new Document(new Paragraph(new Text('{{{nowiki }}}'))), $doc);
        
        $doc = $parser->parse("{{{~nowiki\n}}}");
        $this->assertEquals(new Document(new NoWiki(array(new Text("~nowiki\n")))), $doc);
    }
}