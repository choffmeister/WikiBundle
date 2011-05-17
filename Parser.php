<?php

namespace Thekwasti\WikiBundle;

use Thekwasti\WikiBundle\Tree\Document;
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
    private $document;
    
    public function __construct($markup)
    {
        if (!is_string($markup)) {
            throw new \InvalidArgumentException('$markup must be a string');
        }
        
        $markup = str_replace("\r\n", "\n", $markup);
        $lines = explode("\n", $markup);
        $this->tree = new Document();
        
        $children = array();
        foreach ($lines as $line) {
            $children[] = $this->parseLine($line);
        }
        
        $this->document = new Document($children);
    }
        
    private function parseLine($line)
    {
        if (trim($line) === '') {
            return new EmptyLine();
        } else if (preg_match('/^(=+)(.+)$/', $line, $match)) {
            return new Headline(strlen($match[1]), $this->parseText($match[2]));
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
                    new Link($split[0], new Text($split[0]))
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
        
        $result = array();
        
        if ($hasPre) {
            $result[] = $this->parseText(substr($text, 0, $i));
        }
        
        $result[] = $token;
        
        if ($hasPost) {
            $result[] = $this->parseText(substr($text, $i + $j));
        }
        
        return $result;
    }
    
    public function getDocument()
    {
        return $this->document;
    }
}

