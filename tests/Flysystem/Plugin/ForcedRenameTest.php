<?php

use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use Lhridley\Flysystem\Plugin\ForcedRename;

class ForcedRenameTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $plugin = new ForcedRename();
        $adapter = $this->prophesize(AdapterInterface::class);
        $plugin->setFilesystem(new Filesystem($adapter->reveal()));

        $adapter->has('source')->willReturn(true);
        $adapter->has('dest')->willReturn(true);
        $adapter->getMetadata('source')->willReturn(['type' => 'file']);
        $adapter->getMetadata('dest')->willReturn(['type' => 'file']);
        $adapter->delete('dest')->willReturn(false);

        $this->assertFalse($plugin->handle('source', 'dest'));
    }
}
