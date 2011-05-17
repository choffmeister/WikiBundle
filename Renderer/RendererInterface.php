<?php

namespace Thekwasti\WikiBundle\Renderer;

use Thekwasti\WikiBundle\Tree\NodeInterface;

interface RendererInterface
{
    /**
     * Renders a single element or an array of elements.
     * 
     * @param NodeInterface|array $element An NodeInterface or an array of NodeInterface objects
     * 
     * @return string The rendered string
     */
    function render($element);
}
