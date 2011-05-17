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
    public function render(NodeInterface $element)
    {
        return $this->renderRecursion($element);
    }
    
    private function renderRecursion(NodeInterface $element, $depth = 0)
    {
        if ($element instanceof Chain) {
            $result = str_repeat('    ', $depth) . "Chain\n";
            
            foreach ($element->getElements() as $subElement) {
                $result .= $this->renderRecursion($subElement, $depth + 1);
            }
            
            return $result;
        } else if ($element instanceof Text) {
            return str_repeat('    ', $depth) . "Text\n";
        } else if ($element instanceof EmptyLine) {
            return str_repeat('    ', $depth) . "EmptyLine\n";
        } else if ($element instanceof HorizontalRule) {
            return str_repeat('    ', $depth) . "HorizontalRule\n";
        } else if ($element instanceof Headline) {
            return str_repeat('    ', $depth) . "Headline\n" . $this->renderRecursion($element->getContent(), $depth + 1);
        } else if ($element instanceof Bold) {
            return str_repeat('    ', $depth) . "Bold\n" . $this->renderRecursion($element->getContent(), $depth + 1);
        } else if ($element instanceof Italic) {
            return str_repeat('    ', $depth) . "Italic\n" . $this->renderRecursion($element->getContent(), $depth + 1);
        } else if ($element instanceof Link) {
            return str_repeat('    ', $depth) . "Link\n" . $this->renderRecursion($element->getContent(), $depth + 1);
        } else {
            return '';
        }
    }
    
    public function renderPre()
    {
        return '';
    }
    
    public function renderPost()
    {
        return '';
    }
}
