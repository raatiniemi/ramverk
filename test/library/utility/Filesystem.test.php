<?php
namespace Me\Raatiniemi\Ramverk\Utility {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Test\Utility;

	function is_dir($filename) {
		if(Utility\Filesystem::$mockIsDirectory) {
			return Utility\Filesystem::$returnValueIsDirectory;
		} else {
			return call_user_func('\\is_dir', func_get_args());
		}
	}

	function is_file($filename) {
		if(Utility\Filesystem::$mockIsFile) {
			return Utility\Filesystem::$returnValueIsFile;
		} else {
			return call_user_func('\\is_file', func_get_args());
		}
	}

	function is_readable($filename) {
		if(Utility\Filesystem::$mockIsReadable) {
			return Utility\Filesystem::$returnValueIsReadable;
		} else {
			return call_user_func('\\is_readable', func_get_args());
		}
	}

	function is_writeable($filename) {
		if(Utility\Filesystem::$mockIsWriteable) {
			return Utility\Filesystem::$returnValueIsWriteable;
		} else {
			return call_user_func('\\is_writable', func_get_args());
		}
	}

	function mkdir($pathname, $mode = 0777, $recursive = false) {
		if(Utility\Filesystem::$mockMakeDirectory) {
			return Utility\Filesystem::$returnValueMakeDirectory;
		} else {
			return call_user_func('\\mkdir', func_get_args());
		}
	}
}

namespace Me\Raatiniemi\Ramverk\Test\Utility {
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
	class Filesystem extends \PHPUnit_Framework_TestCase {
		private $class = 'Me\\Raatiniemi\\Ramverk\\Utility\\Filesystem';

		public static $mockIsDirectory;
		public static $returnValueIsDirectory;

		public static $mockIsFile;
		public static $returnValueIsFile;

		public static $mockIsReadable;
		public static $returnValueIsReadable;

		public static $mockIsWriteable;
		public static $returnValueIsWriteable;

		public static $mockMakeDirectory;
		public static $returnValueMakeDirectory;

		public function setUp() {
			Filesystem::$mockIsDirectory = true;
			Filesystem::$returnValueIsDirectory = true;

			Filesystem::$mockIsFile = true;
			Filesystem::$returnValueIsFile = true;

			Filesystem::$mockIsReadable = true;
			Filesystem::$returnValueIsReadable = true;

			Filesystem::$mockIsWriteable = true;
			Filesystem::$returnValueIsWriteable = true;

			Filesystem::$mockMakeDirectory = true;
			Filesystem::$returnValueMakeDirectory = true;
		}

		public function testIsDirectoryWithFailure() {
			Filesystem::$returnValueIsDirectory = false;

			$fs = new Utility\Filesystem;
			$this->assertFalse($fs->isDirectory('foobar'));
		}

		public function testIsDirectoryWithSuccess() {
			$fs = new Utility\Filesystem;
			$this->assertTrue($fs->isDirectory('foobar'));
		}

		public function testIsFileWithFailure() {
			Filesystem::$returnValueIsFile = false;

			$fs = new Utility\Filesystem;
			$this->assertFalse($fs->isFile('foobar'));
		}

		public function testIsFileWithSuccess() {
			$fs = new Utility\Filesystem;
			$this->assertTrue($fs->isFile('foobar'));
		}

		public function testIsReadableWithFailure() {
			Filesystem::$returnValueIsReadable = false;

			$fs = new Utility\Filesystem;
			$this->assertFalse($fs->isReadable('foobar'));
		}

		public function testIsReadableWithSuccess() {
			$fs = new Utility\Filesystem;
			$this->assertTrue($fs->isReadable('foobar'));
		}

		public function testIsWriteableWithFailure() {
			Filesystem::$returnValueIsWriteable = false;

			$fs = new Utility\Filesystem;
			$this->assertFalse($fs->isWriteable('foobar'));
		}

		public function testIsWriteableWithSuccess() {
			$fs = new Utility\Filesystem;
			$this->assertTrue($fs->isWriteable('foobar'));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 * @expectedExceptionMessage
		 */
		public function testMakeDirectoryWithReadableFile() {
			$fs = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Utility\\Filesystem')
				->setMethods(array('isReadable', 'isDirectory'))
				->getMock();

			$fs->expects($this->once())
				->method('isReadable')
				->with('foobar')
				->willReturn(true);

			$fs->expects($this->once())
				->method('isDirectory')
				->with('foobar')
				->willReturn(false);

			$fs->makeDirectory('foobar');
		}

		public function testMakeDirectoryWithReadableDirectory() {
			$fs = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Utility\\Filesystem')
				->setMethods(array('isReadable', 'isDirectory'))
				->getMock();

			$fs->expects($this->once())
				->method('isReadable')
				->with('foobar')
				->willReturn(true);

			$fs->expects($this->once())
				->method('isDirectory')
				->with('foobar')
				->willReturn(true);

			$this->assertTrue($fs->makeDirectory('foobar'));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 * @expectedExceptionMessage
		 */
		public function testMakeDirectoryWithDirectoryFailure() {
			Filesystem::$returnValueMakeDirectory = false;

			$fs = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Utility\\Filesystem')
				->setMethods(array('isReadable'))
				->getMock();

			$fs->expects($this->once())
				->method('isReadable')
				->with('foobar')
				->willReturn(false);

			$fs->makeDirectory('foobar');
		}

		public function testMakeDirectoryWithDirectorySuccess() {
			$fs = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Utility\\Filesystem')
				->setMethods(array('isReadable'))
				->getMock();

			$fs->expects($this->once())
				->method('isReadable')
				->with('foobar')
				->willReturn(false);

			$this->assertTrue($fs->makeDirectory('foobar'));
		}
	}
}
// End of file: Filesystem.test.php
// Location: test/library/utility/Filesystem.test.php