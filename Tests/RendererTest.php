<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\Tree\Document;

use Thekwasti\WikiBundle\Renderer\DebugRenderer;
use Thekwasti\WikiBundle\Renderer\LatexRenderer;
use Thekwasti\WikiBundle\Renderer\XhtmlRenderer;
use Thekwasti\WikiBundle\UrlGenerator;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $urlGenerator = new UrlGenerator('/{wiki}/{page}');
        
        $renderer = new XhtmlRenderer($urlGenerator);
        $renderer->render(new Document());
        $renderer->render(new Document(), 'test');
        
        $renderer = new LatexRenderer($urlGenerator);
        $renderer->render(new Document());
        $renderer->render(new Document(), 'test');
        
        $renderer = new DebugRenderer($urlGenerator);
        $renderer->render(new Document());
        $renderer->render(new Document(), 'test');
    }
}