<?php

use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use Lhridley\FlysystemStreamWrapper;
use Lhridley\Tests\NoVisibilityLocal;
use PHPUnit\Framework\TestCase;

class FlysystemStreamWrapperTest extends TestCase {
    public function testRegister()
    {
        $filesystem = new Filesystem(new NullAdapter());
        $this->assertTrue(FlysystemStreamWrapper::register('test', $filesystem));

        $this->assertTrue(in_array('test', stream_get_wrappers(), true));

        $this->assertTrue(stream_is_local('test://'));

        // Registering twice should be a noop.
        $this->assertFalse(FlysystemStreamWrapper::register('test', $filesystem));

        $this->assertTrue(FlysystemStreamWrapper::unregister('test'));

        $this->assertTrue(FlysystemStreamWrapper::register('test', $filesystem, [], STREAM_IS_URL));

        $this->assertFalse(stream_is_local('test://'));

        $this->assertTrue(FlysystemStreamWrapper::unregister('test'));
        $this->assertFalse(FlysystemStreamWrapper::unregister('test'));
    }

    public function testNoVisibility()
    {
        $filesystem = new Filesystem(new NoVisibilityLocal(sys_get_temp_dir()));
        FlysystemStreamWrapper::register('vis', $filesystem);
        $this->assertTrue(touch('vis://test.txt'));
        $this->assertTrue(chmod('vis://test.txt', 0777));
        $filesystem->delete('test.txt');
    }

    public function testGetRegisteredProtocols()
    {
        $filesystem = new Filesystem(new NullAdapter());
        FlysystemStreamWrapper::register('test1', $filesystem);
        FlysystemStreamWrapper::register('test2', $filesystem);

        $this->assertSame(['test1', 'test2'], FlysystemStreamWrapper::getRegisteredProtocols());
    }

    public function testUnregisterAll()
    {
        $filesystem = new Filesystem(new NullAdapter());
        FlysystemStreamWrapper::register('test1', $filesystem);
        FlysystemStreamWrapper::register('test2', $filesystem);

        $this->assertTrue(in_array('test1', stream_get_wrappers(), true));
        $this->assertTrue(in_array('test2', stream_get_wrappers(), true));

        FlysystemStreamWrapper::unregisterAll();

        $this->assertFalse(in_array('test1', stream_get_wrappers(), true));
        $this->assertFalse(in_array('test2', stream_get_wrappers(), true));
    }

    public function tearDown(): void
    {
        FlysystemStreamWrapper::unregisterAll();
    }
}
