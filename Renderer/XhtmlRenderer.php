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

class XhtmlRenderer implements RendererInterface
{
    public function render(NodeInterface $element)
    {
        if ($element instanceof Chain) {
            $result = '';
            
            foreach ($element->getElements() as $subElement) {
                $result .= $this->render($subElement);
            }
            
            return $result;
        } else if ($element instanceof Text) {
            return $element->getText();
        } else if ($element instanceof EmptyLine) {
            return "\n<br/>\n";
        } else if ($element instanceof HorizontalRule) {
            return "\n<hr/>\n";
        } else if ($element instanceof Headline) {
            return sprintf("<h%d>%s</h%d>\n",
                $element->getLevel(),
                $this->render($element->getContent()),
                $element->getLevel()
            );
        } else if ($element instanceof Bold) {
            return sprintf('<strong>%s</strong>', $this->render($element->getContent()));
        } else if ($element instanceof Italic) {
            return sprintf('<em>%s</em>', $this->render($element->getContent()));
        } else if ($element instanceof Link) {
            return sprintf('<a href="%s">%s</a>', $element->getDestination(), $this->render($element->getContent()));
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
