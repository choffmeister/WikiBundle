<?php

namespace Thekwasti\WikiBundle;

class Lexer
{
    const T_TEXT = 1;
    const T_NEWLINE = 10;
    const T_EMPTYLINE = 11;
    const T_HEADLINE_1 = 51;
    const T_HEADLINE_2 = 52;
    const T_HEADLINE_3 = 53;
    const T_HEADLINE_4 = 54;
    const T_HEADLINE_5 = 55;
    const T_HEADLINE_6 = 56;
    const T_HORIZONTAL_RULE = 61;
    const T_BOLD = 101;
    const T_ITALIC = 102;
    const T_LINK_OPEN = 111;
    const T_LINK_CLOSE = 112;
    const T_LINK_DELIM = 113;
    const T_LIST_BULLET_ITEM_1 = 121;
    const T_LIST_BULLET_ITEM_2 = 122;
    const T_LIST_BULLET_ITEM_3 = 123;
    const T_LIST_BULLET_ITEM_4 = 124;
    const T_LIST_BULLET_ITEM_5 = 125;
    const T_LIST_BULLET_ITEM_6 = 126;
    const T_LIST_SHARP_ITEM_1 = 131;
    const T_LIST_SHARP_ITEM_2 = 132;
    const T_LIST_SHARP_ITEM_3 = 133;
    const T_LIST_SHARP_ITEM_4 = 134;
    const T_LIST_SHARP_ITEM_5 = 135;
    const T_LIST_SHARP_ITEM_6 = 136;
    const T_NOWIKI_OPEN = 1001;
    const T_NOWIKI_CLOSE = 1002;
    const T_NOWIKI_INLINE_OPEN = 1003;
    const T_NOWIKI_INLINE_CLOSE = 1004;
    
    private $patterns = array(
        "\n{2,}",
        "\n",
        '^=+',
        '^\*+',
        '^\#+',
        '^\-{4,}',
        '\*\*',
        '//',
        '\[\[',
        '\]\]',
        '\|',
        '\{\{\{',
        '\}\}\}',
    );
    
    public function lex($markup)
    {
        if (!is_string($markup)) {
            throw new \InvalidArgumentException('$markup must be a string');
        }
        
        $markup = str_replace("\r\n", "\n", $markup);
        $markup = preg_replace("/^\s*$/m", "", $markup);
        
        $regex = '~(' . implode(')|(', $this->patterns) . ')~im';
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
        } else if ($isLineBeginning && $value[0] == '=') {
            $level = strlen(trim($value));
            if ($level > 6) $level = 6; 
            return self::T_HEADLINE_1 + $level - 1;
        } else if ($isLineBeginning && $value[0] == '*') {
            $level = strlen(trim($value));
            if ($level > 6) $level = 6; 
            return self::T_LIST_BULLET_ITEM_1 + $level - 1;
        } else if ($isLineBeginning && $value[0] == '#') {
            $level = strlen(trim($value));
            if ($level > 6) $level = 6; 
            return self::T_LIST_SHARP_ITEM_1 + $level - 1;
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
            return self::T_LINK_DELIM;
        } else if ($value == '{{{') {
            return $isLineBeginning ? self::T_NOWIKI_OPEN : self::T_NOWIKI_INLINE_OPEN;
        } else if ($value == '}}}') {
            return $isLineBeginning ? self::T_NOWIKI_CLOSE : self::T_NOWIKI_INLINE_CLOSE;
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
        
        return $token;
    }
}
