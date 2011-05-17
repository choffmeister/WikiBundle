<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\Parser;
use Thekwasti\WikiBundle\Renderer\XhtmlRenderer;
use Thekwasti\WikiBundle\Renderer\LatexRenderer;
use Thekwasti\WikiBundle\Renderer\DebugRenderer;

class ParserRendererTest extends \PHPUnit_Framework_TestCase
{
    private $markup1 = <<<MU
= Headings 1 //top//
Lorem **ipsum** dolor sit amet, consetetur sadipscing elitr.
Lorem //ipsum// dolor sit amet, consetetur sadipscing elitr.

Lorem **//ipsum dolor//** sit amet, consetetur sadipscing elitr.






== Headings 2
|=First|=Second|
|1 asda|1b|
|2a|2b|

=== Headings 3
Click [[mypage]] to watch me!
Click [[http://www.youtube.com | Youtube]] to watch videos!

=== ASDASD
[[asdasd]]

asdsad
------
asdsad

MU;

    private $markup2 = 'Pre **//bOlD//** post [[link]] [[link|to]]';

    private $markup3 = '[[link]][[link|to]]';
    
    public function test()
    {
        $parser = new Parser($this->markup3);
        $parser = new Parser($this->markup2);
        $parser = new Parser($this->markup1);
        
        $renderer = new XhtmlRenderer();
        
        $renderer = new DebugRenderer();
        echo "\n###\n" . $renderer->renderPre() . $renderer->render($parser->getTree()) . $renderer->renderPost() . "\n###\n";
        
        $renderer = new LatexRenderer();
        $latex = $renderer->renderPre() . $renderer->render($parser->getTree()) . $renderer->renderPost();
        
        file_put_contents('/tmp/latex.tex', $latex);
    }
}
