<?php

/*
 * This file is part of WikiBundle
 *
 * (c) Christian Hoffmeister <choffmeister.github@googlemail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Thekwasti\WikiBundle\Twig\Extension;

use Thekwasti\WikiBundle\Parser;

use Symfony\Component\DependencyInjection\Container;

/**
 * WikiExtension
 * 
 * @author Christian Hoffmeister <choffmeister.github@googlemail.com>
 */
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
