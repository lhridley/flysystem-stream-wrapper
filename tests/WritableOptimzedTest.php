<?php

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Lhridley\FlysystemStreamWrapper;

class WritableOptimzedTest extends StreamOperationTest
{
    public function setUp(): void
    {
        $this->testDir = __DIR__ . '/testdir';

        $filesystem = new Filesystem(new Local(__DIR__));
        $filesystem->deleteDir('testdir');
        $filesystem->createDir('testdir');

        $writable = new WritableLocal($this->testDir, \LOCK_EX, 0002, $this->perms);
        $this->filesystem = new Filesystem($writable);
        FlysystemStreamWrapper::register('flysystem', $this->filesystem);
    }
}
