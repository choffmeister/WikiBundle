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

/**
 * RendererInterface
 * 
 * @author Christian Hoffmeister <choffmeister.github@googlemail.com>
 */
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
