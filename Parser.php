<?php

namespace Thekwasti\WikiBundle;

use Thekwasti\WikiBundle\Tree\NodeInterface;

use Thekwasti\WikiBundle\Tree\HorizontalRule;
use Thekwasti\WikiBundle\Tree\Headline;
use Thekwasti\WikiBundle\Tree\EmptyLine;
use Thekwasti\WikiBundle\Tree\Chain;
use Thekwasti\WikiBundle\Tree\Text;
use Thekwasti\WikiBundle\Tree\Bold;
use Thekwasti\WikiBundle\Tree\Italic;
use Thekwasti\WikiBundle\Tree\Link;

final class Parser
{
    private $markup;
    private $tree;
    
    public function __construct($markup)
    {
        if (!is_string($markup)) {
            throw new \InvalidArgumentException('$markup must be a string');
        }
        
        $markup = str_replace("\r\n", "\n", $markup);
        $lines = explode("\n", $markup);
        $this->tree = new Chain();
        
        foreach ($lines as $line) {
            $this->tree->addElement($this->parseLine($line));
        }
    }
    
    private function parseLine($line)
    {
        if ($line === '') {
            return new EmptyLine();
        } else if (preg_match('/^(=+)(.+)$/', $line, $match)) {
            return new Headline($this->parseText($match[2]), strlen($match[1]));
        } else if (preg_match('/^\-{4,}$/', $line, $match)) {
            return new HorizontalRule();
        } else {
            return $this->parseText($line . ' ');
        }
    }
    
    private function parseText($text)
    {
        if (preg_match('/\*\*([^\*]*)\*\*/', $text, $match, PREG_OFFSET_CAPTURE)) {
            return $this->splitText($text, $match[0][1], strlen($match[0][0]),
                new Bold($this->parseText($match[1][0]))
            );
        } else if (preg_match('#//([^/]*)//#', $text, $match, PREG_OFFSET_CAPTURE)) {
            return $this->splitText($text, $match[0][1], strlen($match[0][0]),
                new Italic($this->parseText($match[1][0]))
            );
        } else if (preg_match('/\[\[([^\]]*)\]\]/', $text, $match, PREG_OFFSET_CAPTURE)) {
            $split = explode("|", $match[1][0]);
            
            if (count($split) == 1) {
                return $this->splitText($text, $match[0][1], strlen($match[0][0]),
                    new Link($split[0])
                );
            } else {
                return $this->splitText($text, $match[0][1], strlen($match[0][0]),
                    new Link($split[0], $this->parseText($split[1]))
                );
            }
        }
        
        return new Text($text);
    }
    
    private function splitText($text, $i, $j, NodeInterface $token)
    {
        $len = strlen($text);
        
        $hasPre = $i > 0 ? true : false;
        $hasPost = $i + $j < $len ? true : false;
        
        if (!$hasPre && !$hasPost) {
            return $token;
        }
        
        $result = new Chain();
        
        if ($hasPre) {
            $result->addElement($this->parseText(substr($text, 0, $i)));
        }
        
        $result->addElement($token);
        
        if ($hasPost) {
            $result->addElement($this->parseText(substr($text, $i + $j)));
        }
        
        return $result;
    }
    
    public function getTree()
    {
        return $this->tree;
    }
}

