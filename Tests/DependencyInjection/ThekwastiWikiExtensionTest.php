<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\DependencyInjection\ThekwastiWikiExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ThekwastiWikiExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $cb = new ContainerBuilder();
        $ext = new ThekwastiWikiExtension();
        $ext->load(array(), $cb);
    }
}