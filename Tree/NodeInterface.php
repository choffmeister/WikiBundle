<?php

namespace Thekwasti\WikiBundle\Tree;

interface NodeInterface extends \Serializable
{
    public function getChildren();
}
