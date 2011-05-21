<?php
/*
 * This file is part of WikiBundle
 *
 * (c) Christian Hoffmeister <choffmeister.github@googlemail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Thekwasti\WikiBundle;

/**
 * Lexer
 * 
 * @author Christian Hoffmeister <choffmeister.github@googlemail.com>
 */
class Lexer
{
    const T_TEXT = 1;
    const T_ESCAPER = 2;
    const T_NEWLINE = 10;
    const T_EMPTYLINE = 11;
    const T_BREAKLINE = 12;
    const T_PIPE = 20;
    const T_HEADLINE = 51;
    const T_HORIZONTAL_RULE = 61;
    const T_BOLD = 101;
    const T_ITALIC = 102;
    const T_LINK_OPEN = 111;
    const T_LINK_CLOSE = 112;
    const T_LIST_BULLET_ITEM = 121;
    const T_LIST_SHARP_ITEM = 131;
    const T_TABLE_CELL_HEAD = 201;
    const T_NOWIKI_OPEN = 1001;
    const T_NOWIKI_CLOSE = 1002;
    const T_NOWIKI_INLINE_OPEN = 1003;
    const T_NOWIKI_INLINE_CLOSE = 1004;
    
    private $patterns = array(
        "\n{2,}",
        "\n",
        '~.',
        '\\\\\\\\',
        '^=+',
        '^\*+',
        '^\#+',
        '^\-{4,}',
        '\*\*',
        '//',
        '\[\[',
        '\]\]',
        '\|=',
        '\|',
        '\{\{\{',
        '\}\}\}',
    );
    
    //FIXME empty lines at the end are lexed as newline
    public function lex($markup)
    {
        if (!is_string($markup)) {
            throw new \InvalidArgumentException('$markup must be a string');
        }
        
        $markup = str_replace("\r\n", "\n", $markup);
        $markup = preg_replace("/^\s*$/m", "", $markup);
        
        $regex = ';(' . implode(')|(', $this->patterns) . ');im';
        $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches = preg_split($regex, $markup, -1, $flags);
        
        $previous = null;
        $tokens = array();
        
        foreach ($matches as $match) {
            $type = $this->getType($match[0], $previous);
            
            $previous = array(
                'value'  => $match[0],
                'type'   => $type,
                'offset' => $match[1],
            );
            $tokens[] = $previous;
        }
        
        return $tokens;
    }
    
    protected function getType(&$value, $previous)
    {
        $isLineBeginning = $previous === null || $previous['type'] == self::T_NEWLINE || $previous['type'] == self::T_EMPTYLINE;
        
        if ($value == "\n") {
            return self::T_NEWLINE;
        } else if (preg_match("/^\n{2,}$/", $value)) {
            return self::T_EMPTYLINE;
        } else if (strlen($value) == 2 && $value[0] == '~') {
            return self::T_ESCAPER;
        } else if ($value == "\\\\") {
            return self::T_BREAKLINE;
        } else if ($isLineBeginning && $value[0] == '=') {
            return self::T_HEADLINE;
        } else if ($isLineBeginning && $value[0] == '*') {
            return self::T_LIST_BULLET_ITEM;
        } else if ($isLineBeginning && $value[0] == '#') {
            return self::T_LIST_SHARP_ITEM;
        } else if ($isLineBeginning && strlen($value) >= 4 && substr($value, 0, 4) == '----') {
            return self::T_HORIZONTAL_RULE;
        } else if ($value == '**') {
            return self::T_BOLD;
        } else if ($value == '//') {
            return self::T_ITALIC;
        } else if ($value == '[[') {
            return self::T_LINK_OPEN;
        } else if ($value == ']]') {
            return self::T_LINK_CLOSE;
        } else if ($value == '|') {
            return self::T_PIPE;
        } else if ($value == '{{{') {
            return $isLineBeginning ? self::T_NOWIKI_OPEN : self::T_NOWIKI_INLINE_OPEN;
        } else if ($value == '}}}') {
            return $isLineBeginning ? self::T_NOWIKI_CLOSE : self::T_NOWIKI_INLINE_CLOSE;
        } else if ($value == '|=') {
            return self::T_TABLE_CELL_HEAD;
        }
        
        return self::T_TEXT;
    }
    
    /**
     * Gets the literal for a given token.
     *
     * @param integer $token
     * @return string
     */
    public function getLiteral($token)
    {
        $className = get_class($this);
        $reflClass = new \ReflectionClass($className);
        $constants = $reflClass->getConstants();
        
        foreach ($constants as $name => $value) {
            if ($value === $token) {
                return $name;
            }
        }
        
        throw new \InvalidArgumentException(sprintf('Unknown token %d', $token));
    }
}
