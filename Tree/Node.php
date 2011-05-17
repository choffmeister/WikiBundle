<?php

namespace Thekwasti\WikiBundle\Tree;

class Node implements NodeInterface
{
    private $children = array();
    
    public function __construct($children = array())
    {
        if (!is_array($children)) {
            $children = array($children);
        }
        
        $children = self::flattenArray($children);
        
        foreach ($children as $child) {
            if (!$child instanceof NodeInterface) {
                throw new \InvalidArgumentException(sprintf('$children must be an array of NodeInterface objects. Found an %s element', gettype($child) == 'object' ? get_class($child) : gettype($child)));
            }
        }
        
        $this->children = $children;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
    
    private static function flattenArray($array, $result = array())
    {
        for ($i = 0; $i < count($array); $i++) {
            if (is_array($array[$i])) {
                $result = self::flattenArray($array[$i], $result);
            } else {
                if ($array[$i]) {
                    $result[] = $array[$i];
                }
            }
        }
        
        return $result;
    }
}
