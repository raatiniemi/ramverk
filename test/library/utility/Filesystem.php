<?php
namespace Me\Raatiniemi\Ramverk\Utility;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Test\Utility;

function is_dir()
{
    if (Utility\Filesystem::$mockIsDirectory) {
        return Utility\Filesystem::$valueIsDirectory;
    } else {
        return call_user_func('\\is_dir', func_get_args());
    }
}

function is_file()
{
    if (Utility\Filesystem::$mockIsFile) {
        return Utility\Filesystem::$valueIsFile;
    } else {
        return call_user_func('\\is_file', func_get_args());
    }
}

function is_readable()
{
    if (Utility\Filesystem::$mockIsReadable) {
        return Utility\Filesystem::$valueIsReadable;
    } else {
        return call_user_func('\\is_readable', func_get_args());
    }
}

function is_writable()
{
    if (Utility\Filesystem::$mockisWritable) {
        return Utility\Filesystem::$valueisWritable;
    } else {
        return call_user_func('\\is_writable', func_get_args());
    }
}

function mkdir()
{
    if (Utility\Filesystem::$mockMakeDirectory) {
        return Utility\Filesystem::$valueMakeDirectory;
    } else {
        return call_user_func('\\mkdir', func_get_args());
    }
}

namespace Me\Raatiniemi\Ramverk\Test\Utility;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Utility;

/**
 * @package Ramverk
 * @subpackage Test
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Filesystem extends \PHPUnit_Framework_TestCase
{
    private $trait = 'Me\\Raatiniemi\\Ramverk\\Utility\\Filesystem';

    public static $mockIsDirectory;
    public static $valueIsDirectory;

    public static $mockIsFile;
    public static $valueIsFile;

    public static $mockIsReadable;
    public static $valueIsReadable;

    public static $mockisWritable;
    public static $valueisWritable;

    public static $mockMakeDirectory;
    public static $valueMakeDirectory;

    public function setUp()
    {
        Filesystem::$mockIsDirectory = true;
        Filesystem::$valueIsDirectory = true;

        Filesystem::$mockIsFile = true;
        Filesystem::$valueIsFile = true;

        Filesystem::$mockIsReadable = true;
        Filesystem::$valueIsReadable = true;

        Filesystem::$mockisWritable = true;
        Filesystem::$valueisWritable = true;

        Filesystem::$mockMakeDirectory = true;
        Filesystem::$valueMakeDirectory = true;
    }

    public function testIsDirectoryWithFailure()
    {
        Filesystem::$valueIsDirectory = false;

        $stub = $this->getMockForTrait($this->trait);
        $this->assertFalse($stub->isDirectory('foobar'));
    }

    public function testIsDirectoryWithSuccess()
    {
        $stub = $this->getMockForTrait($this->trait);
        $this->assertTrue($stub->isDirectory('foobar'));
    }

    public function testIsFileWithFailure()
    {
        Filesystem::$valueIsFile = false;

        $stub = $this->getMockForTrait($this->trait);
        $this->assertFalse($stub->isFile('foobar'));
    }

    public function testIsFileWithSuccess()
    {
        $stub = $this->getMockForTrait($this->trait);
        $this->assertTrue($stub->isFile('foobar'));
    }

    public function testIsReadableWithFailure()
    {
        Filesystem::$valueIsReadable = false;

        $stub = $this->getMockForTrait($this->trait);
        $this->assertFalse($stub->isReadable('foobar'));
    }

    public function testIsReadableWithSuccess()
    {
        $stub = $this->getMockForTrait($this->trait);
        $this->assertTrue($stub->isReadable('foobar'));
    }

    public function testisWritableWithFailure()
    {
        Filesystem::$valueisWritable = false;

        $stub = $this->getMockForTrait($this->trait);
        $this->assertFalse($stub->isWritable('foobar'));
    }

    public function testisWritableWithSuccess()
    {
        $stub = $this->getMockForTrait($this->trait);
        $this->assertTrue($stub->isWritable('foobar'));
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage
     */
    public function testMakeDirectoryWithReadableFile()
    {
        // Since traits only mock abstract methods by default we have to supply
        // the mocked methods to the `getMockForTrait`-method, arguments is:
        // 1: Trait name
        // 2: Arguments
        // 3: Mock class name
        // 4: Call original constructor
        // 5: Call original clone
        // 6: Call autoload
        // 7: Mocked methods
        // 8: Clone arguments
        $methods = array('isReadable', 'isDirectory');
        $stub = $this->getMockForTrait($this->trait, array(), '', true, true, true, $methods);

        $stub->expects($this->once())
            ->method('isReadable')
            ->with('foobar')
            ->willReturn(true);

        $stub->expects($this->once())
            ->method('isDirectory')
            ->with('foobar')
            ->willReturn(false);

        $stub->makeDirectory('foobar');
    }

    public function testMakeDirectoryWithReadableDirectory()
    {
        // Since traits only mock abstract methods by default we have to supply
        // the mocked methods to the `getMockForTrait`-method, arguments is:
        // 1: Trait name
        // 2: Arguments
        // 3: Mock class name
        // 4: Call original constructor
        // 5: Call original clone
        // 6: Call autoload
        // 7: Mocked methods
        // 8: Clone arguments
        $methods = array('isReadable', 'isDirectory');
        $stub = $this->getMockForTrait($this->trait, array(), '', true, true, true, $methods);

        $stub->expects($this->once())
            ->method('isReadable')
            ->with('foobar')
            ->willReturn(true);

        $stub->expects($this->once())
            ->method('isDirectory')
            ->with('foobar')
            ->willReturn(true);

        $this->assertTrue($stub->makeDirectory('foobar'));
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage
     */
    public function testMakeDirectoryWithDirectoryFailure()
    {
        Filesystem::$valueMakeDirectory = false;

        // Since traits only mock abstract methods by default we have to supply
        // the mocked methods to the `getMockForTrait`-method, arguments is:
        // 1: Trait name
        // 2: Arguments
        // 3: Mock class name
        // 4: Call original constructor
        // 5: Call original clone
        // 6: Call autoload
        // 7: Mocked methods
        // 8: Clone arguments
        $methods = array('isReadable');
        $stub = $this->getMockForTrait($this->trait, array(), '', true, true, true, $methods);

        $stub->expects($this->once())
            ->method('isReadable')
            ->with('foobar')
            ->willReturn(false);

        $stub->makeDirectory('foobar');
    }

    public function testMakeDirectoryWithDirectorySuccess()
    {
        // Since traits only mock abstract methods by default we have to supply
        // the mocked methods to the `getMockForTrait`-method, arguments is:
        // 1: Trait name
        // 2: Arguments
        // 3: Mock class name
        // 4: Call original constructor
        // 5: Call original clone
        // 6: Call autoload
        // 7: Mocked methods
        // 8: Clone arguments
        $methods = array('isReadable');
        $stub = $this->getMockForTrait($this->trait, array(), '', true, true, true, $methods);

        $stub->expects($this->once())
            ->method('isReadable')
            ->with('foobar')
            ->willReturn(false);

        $this->assertTrue($stub->makeDirectory('foobar'));
    }
}
// End of file: Filesystem.php
// Location: test/library/utility/Filesystem.php
