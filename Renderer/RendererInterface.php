<?php

namespace Thekwasti\WikiBundle\Renderer;

use Thekwasti\WikiBundle\Tree\NodeInterface;

interface RendererInterface
{
    function render(NodeInterface $element);
    function renderPre();
    function renderPost();
}
