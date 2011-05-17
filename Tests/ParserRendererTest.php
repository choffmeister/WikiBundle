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
        $start = microtime(true);
        
        $parser = new Parser($this->markup3);
        $parser = new Parser($this->markup2);
        $parser = new Parser($this->markup1);
        
        $renderer = new DebugRenderer();
        $renderer->render($parser->getDocument());
        
        $renderer = new XhtmlRenderer();
        echo "\n###\n" . $renderer->render($parser->getDocument()) . "\n###\n";
        
        $renderer = new LatexRenderer();
        $latex = $renderer->render($parser->getDocument());
        file_put_contents('/tmp/latex.tex', $latex);
        
        printf('%.3f ms', (microtime(true) - $start) * 1000.0);
    }
}
