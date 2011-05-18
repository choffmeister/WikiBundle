<?php

namespace Thekwasti\WikiBundle\Tree;

interface NodeInterface extends \Serializable
{
    public function addChild(NodeInterface $child);
    public function getChildren();
    public function setChildren($children);
}
