<?php

namespace Thekwasti\WikiBundle\Tests;

class RandomMarkupGenerator extends \PHPUnit_Framework_TestCase
{
    protected $tempName;
    protected $snippets;
    protected $snippetCount;
    protected $alphabet;
    protected $alphabetSize;
    
    public function __construct()
    {
        $this->tempName = 'thekwasti_wikibundle_pdflatex_fuzy_test';
        
        /*
        // full utf-8 alphabet
        $this->alphabet = '';
        for ($i = 0; $i < 0xD800; $i++)
            $this->alphabet .= iconv('UCS-4LE', 'UTF-8', pack('V', $i));
        for ($i = 0xE000; $i < 0xFFFF; $i++)
            $this->alphabet .= iconv('UCS-4LE', 'UTF-8', pack('V', $i));
        */
            
        $this->alphabet  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $this->alphabet .= '+*-/';
        $this->alphabet .= '.!?,;:-';
        $this->alphabet .= '\$%^&_{}#~';
        
        $this->alphabetSize = strlen($this->alphabet);
        $this->snippets = array(
            "=",
            "==",
            "===",
            "[[",
            "]]",
            "|",
            "**",
            "\\\\",
            "//",
            "\n",
            "\n\n",
            "{{{",
            "}}}",
            "|",
        );
        $this->snippetCount = count($this->snippets);
    }
    
    public function generateRandomMarkup()
    {
        $length = rand(0, 1000);
        $markup = '';
        
        for ($i = 0; $i < $length; $i++) {
            $c = rand(0,3);
            
            if ($c == 0) {
                $markup .= $this->snippets[rand(0, $this->snippetCount - 1)];
            } else {
                $markup .= $this->alphabet[rand(0, $this->alphabetSize - 1)];
            }
        }
        
        return $markup;
    }
}

