<?php

/*
 * This file is part of WikiBundle
 *
 * (c) Christian Hoffmeister <choffmeister.github@googlemail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Thekwasti\WikiBundle\Renderer;

use Thekwasti\WikiBundle\Tree\NodeInterface;
use Thekwasti\WikiBundle\Tree\Text;

/**
 * DebugRenderer
 * 
 * @author Christian Hoffmeister <choffmeister.github@googlemail.com>
 */
class DebugRenderer implements RendererInterface
{
    public function render($element, $currentWiki = null)
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
        } else if ($element instanceof Text) {
            return str_repeat('    ', $depth) . substr(get_class($element), strlen('Thekwasti\WikiBundle\Tree') + 1) . ' ' . $element->getText() . "\n" . $this->renderRecursion($element->getChildren(), $depth + 1);
        } else if ($element instanceof NodeInterface) {
            return str_repeat('    ', $depth) . substr(get_class($element), strlen('Thekwasti\WikiBundle\Tree') + 1) . "\n" . $this->renderRecursion($element->getChildren(), $depth + 1);
        } else {
            throw new \Exception(sprintf('Unsupported element of type %s', gettype($element) == 'object' ? get_class($element) : gettype($element)));
        }
    }
}
