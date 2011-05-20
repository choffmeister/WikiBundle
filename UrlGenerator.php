<?php

namespace Thekwasti\WikiBundle;

use Thekwasti\WikiBundle\Tree\Link;

class UrlGenerator
{
    private $pattern;
    private $currentWiki = '';
    
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public function generateUrl(Link $link)
    {
        $destination = trim($link->getDestination());
        
        if (preg_match('#^[a-z]+://.*$#i', $destination)) {
            $result = $destination;
        } else if (preg_match('#^www\..*\.[a-z]{2,4}$#i', $destination)) {
            $result = 'http://' . $destination;
        } else if (preg_match('#^([^:]+):([^:]+)$#i', $destination, $match)) {
            $result = $this->pattern;
            $result = str_replace('{wiki}', $match[1], $result);
            $result = str_replace('{page}', $match[2], $result);
        } else {
            $result = $this->pattern;
            $result = str_replace('{wiki}', $this->currentWiki, $result);
            $result = str_replace('{page}', $destination, $result);
        }
        
        return $result;
    }
    
    public function setCurrentWiki($currentWiki)
    {
        $this->currentWiki = $currentWiki;
    }
    
    public function getCurrentWiki()
    {
        return $this->currentWiki;
    }

    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }
    
    public function getPattern()
    {
        return $this->pattern;
    }
}