<?php

namespace Thekwasti\WikiBundle\Twig\Extension;

use Thekwasti\WikiBundle\Parser;

use Symfony\Component\DependencyInjection\Container;

class WikiExtension extends \Twig_Extension
{
    private $container;
    private $renderer;
    
    public function __construct(Container $container, array $renderer)
    {
        $this->container = $container;
        $this->renderer = $renderer;
    }
    
    public function renderWiki($markup, $renderer = 'Xhtml')
    {
        $parser = new Parser();
        $doc = $parser->parse($markup);
        return $this->renderer[$renderer]->render($doc);
    }
    
    public function renderWikiPrecompiled($precompiled, $renderer = 'Xhtml')
    {
        $doc = serialize($precompiled);
        return $this->renderer[$renderer]->render($doc);
    }
    
    public function getFunctions()
    {
        return array(
            'wiki' => new \Twig_Function_Method($this, 'renderWiki'),
        	'wiki_pc' => new \Twig_Function_Method($this, 'renderWikiPrecompiled'),
        );
    }    
    
    public function getName()
    {
        return 'wiki';
    }
}
