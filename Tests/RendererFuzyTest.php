<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\Parser;
use Thekwasti\WikiBundle\Renderer\DebugRenderer;
use Thekwasti\WikiBundle\Renderer\LatexRenderer;
use Thekwasti\WikiBundle\Renderer\XhtmlRenderer;
use Thekwasti\WikiBundle\UrlGenerator;

class RendererFuzyTest extends \PHPUnit_Framework_TestCase
{
    protected $generator;
    
    public function __construct()
    {
        $this->generator = new RandomMarkupGenerator();
    }
    
    public function testRandomMarkup1()
    {
        for ($i = 0; $i < 25; $i++) {
            $this->assertRenderingSucceed($this->generator->generateRandomMarkup());
        }
    }
    
    public function testRandomMarkup2()
    {
        for ($i = 0; $i < 25; $i++) {
            $this->assertRenderingSucceed($this->generator->generateRandomMarkup());
        }
    }
    
    public function testRandomMarkup3()
    {
        for ($i = 0; $i < 25; $i++) {
            $this->assertRenderingSucceed($this->generator->generateRandomMarkup());
        }
    }
    
    public function testRandomMarkup4()
    {
        for ($i = 0; $i < 25; $i++) {
            $this->assertRenderingSucceed($this->generator->generateRandomMarkup());
        }
    }
    
    public function assertRenderingSucceed($markup)
    {
        $parser = new Parser();
        $doc = $parser->parse($markup);
        
        $urlGenerator = new UrlGenerator('http://invalid.domain/{wiki}/{page}');
        $urlGenerator->setCurrentWiki('test');
        
        $renderer = new XhtmlRenderer($urlGenerator);
        $latex = $renderer->render($doc);
        
        $renderer = new DebugRenderer();
        $latex = $renderer->render($doc);
        
        $renderer = new LatexRenderer($urlGenerator);
        $latex = $renderer->render($doc);
    }
}
