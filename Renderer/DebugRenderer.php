<?php

namespace Thekwasti\WikiBundle\Renderer;

use Thekwasti\WikiBundle\Tree\HorizontalRule;
use Thekwasti\WikiBundle\Tree\Link;
use Thekwasti\WikiBundle\Tree\Italic;
use Thekwasti\WikiBundle\Tree\Bold;
use Thekwasti\WikiBundle\Tree\EmptyLine;
use Thekwasti\WikiBundle\Tree\NodeInterface;
use Thekwasti\WikiBundle\Tree\Headline;
use Thekwasti\WikiBundle\Tree\Chain;
use Thekwasti\WikiBundle\Tree\Text;

class DebugRenderer implements RendererInterface
{
    public function render($element)
    {
        return $this->renderRecursion($element);
    }
    
    private function renderRecursion($element, $depth = 0)
    {
        if (is_array($element)) {
            $result = '';
            
            foreach ($element as $subElement) {
                $result .= $this->renderRecursion($subElement, $depth);
            }
            
            return $result;
        } else if ($element instanceof NodeInterface) {
            return str_repeat('    ', $depth) . get_class($element) . "\n" . $this->renderRecursion($element->getChildren(), $depth + 1);
        } else {
            throw new \Exception(sprintf('Unsupported element of type %s', gettype($element) == 'object' ? get_class($element) : gettype($element)));
        }
    }
}
