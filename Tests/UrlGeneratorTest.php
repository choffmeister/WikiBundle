<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\UrlGenerator;
use Thekwasti\WikiBundle\Tree\Link;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterSetter()
    {
        $urlGenerator = new UrlGenerator('pre{wiki}mid{page}post');
        $this->assertEquals('pre{wiki}mid{page}post', $urlGenerator->getPattern());
        $urlGenerator->setPattern('pat');
        $this->assertEquals('pat', $urlGenerator->getPattern());
        $urlGenerator->setCurrentWiki('wiKi');
        $this->assertEquals('wiKi', $urlGenerator->getCurrentWiki());
    }
    public function testGeneration()
    {
        $urlGenerator = new UrlGenerator('pre{wiki}mid{page}post');
        $urlGenerator->setCurrentWiki('BOOM');
        
        $this->assertEquals('http://invalid.domain', $urlGenerator->generateUrl(new Link('  http://invalid.domain ')));
        $this->assertEquals('http://www.invalid.doma', $urlGenerator->generateUrl(new Link('www.invalid.doma')));
        $this->assertEquals('preBOOMmidPaGepost', $urlGenerator->generateUrl(new Link('PaGe')));
        $this->assertEquals('preBAAMmidpAgEpost', $urlGenerator->generateUrl(new Link('BAAM:pAgE')));
    }
}