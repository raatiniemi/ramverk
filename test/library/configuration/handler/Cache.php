<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Configuration\Handler;
	use Me\Raatiniemi\Ramverk\Utility;

	/**
	 * Unit test case for the core configuration handler.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Cache extends \PHPUnit_Framework_TestCase {
		private $classFile = 'Me\\Raatiniemi\\Ramverk\\Utility\\File';
		private $stubFile;

		private $classCache = 'Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Cache';
		private $stubCache;

		public function setUp() {
			$this->stubFile = $this->getMockBuilder($this->classFile)
				->setConstructorArgs(array(__FILE__));

			$this->stubCache = $this->getMockBuilder($this->classCache)
				->disableOriginalConstructor();
		}

		public function tearDown() {
			$this->stubFile = null;
			$this->stubCache = null;
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithArrayAsProfile() {
			new Handler\Cache(array(), null);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithIntegerAsProfile() {
			new Handler\Cache(1337, null);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithDoubleAsProfile() {
			new Handler\Cache(13.37, null);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithObjectAsProfile() {
			new Handler\Cache(new \stdClass, null);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithNullAsProfile() {
			new Handler\Cache(null, null);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithEmptyProfile() {
			new Handler\Cache('', null);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithArrayAsContext() {
			new Handler\Cache('foo', array());
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithIntegerAsContext() {
			new Handler\Cache('foo', 1337);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithDoubleAsContext() {
			new Handler\Cache('foo', 13.37);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithObjectAsContext() {
			new Handler\Cache('foo', new \stdClass);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithNullAsContext() {
			new Handler\Cache('foo', null);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testInitializeWithEmptyContext() {
			new Handler\Cache('foo', '');
		}

		/**
		 * @expectedException PHPUnit_Framework_Error
		 */
		public function testGenerateNameWithStringAsFile() {
			$handler = new Handler\Cache('foo', 'bar');
			$handler->generateName('baz');
		}

		/**
		 * @expectedException PHPUnit_Framework_Error
		 */
		public function testGenerateNameWithNullAsFile() {
			$handler = new Handler\Cache('foo', 'bar');
			$handler->generateName(null);
		}

		public function testGenerateName() {
			$file = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('getBasename')
				->willReturn('qux.xml');

			$file->expects($this->once())
				->method('getPathname')
				->willReturn('/baz/qux.xml');

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertEquals(
				$handler->generateName($file),
				'qux.xml_foo_bar_8b3287a42642ee9dd7f4b5b4fd4c8cef18993a43.php'
			);
		}

		public function testGenerateNameFailed() {
			$file = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('getBasename')
				->willReturn('qux.xml');

			$file->expects($this->once())
				->method('getPathname')
				->willReturn('qux.xml');

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertNotEquals(
				$handler->generateName($file),
				'qux.xml_foo_bar_8b3287a42642ee9dd7f4b5b4fd4c8cef18993a43.php'
			);
		}

		/**
		 * @expectedException PHPUnit_Framework_Error
		 */
		public function testIsModifiedWithoutConfigurationFile() {
			$handler = new Handler\Cache('foo', 'bar');
			$handler->isModified(null, null);
		}

		/**
		 * @expectedException PHPUnit_Framework_Error
		 */
		public function testIsModifiedWithoutCacheFile() {
			$file = new Utility\File(__FILE__);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->isModified($file, null);
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testIsModifiedWithoutExistingConfigurationFile() {
			$file = $this->stubFile->getMock();
			$cache = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('isFile')
				->willReturn(false);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->isModified($file, $cache);
		}

		public function testIsModifiedWithoutExistingCacheFile() {
			$file = $this->stubFile->getMock();
			$cache = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$file->method('isReadable')
				->willReturn(true);

			$cache->method('isReadable')
				->willReturn(false);

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertTrue($handler->isModified($file, $cache));
		}

		public function testIsModifiedWithOldCacheFile() {
			$file = $this->stubFile->getMock();
			$cache = $this->stubFile->getMock();

			$file->method('isFile')
				->willReturn(true);

			$file->method('isReadable')
				->willReturn(true);

			$file->method('getMTime')
				->willReturn(time() + 1);

			$cache->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$cache->method('isReadable')
				->willReturn(true);

			$cache->method('getMTime')
				->willReturn(time());

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertTrue($handler->isModified($file, $cache));
		}

		public function testIsModifiedWithNewCache() {
			$file = $this->stubFile->getMock();
			$cache = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$file->method('isReadable')
				->willReturn(true);

			$file->expects($this->once())
				->method('getMTime')
				->willReturn(time());

			$cache->method('isReadable')
				->willReturn(true);

			$cache->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$cache->expects($this->once())
				->method('getMTime')
				->willReturn(time() + 1);

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertFalse($handler->isModified($file, $cache));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testIsModifiedWithoutRegularCachefile() {
			$file = $this->stubFile->getMock();
			$cache = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$cache->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$cache->expects($this->once())
				->method('isFile')
				->willReturn(false);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->isModified($file, $cache);
		}

		public function testReadNonExistingCache() {
			$file = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(false);

			$cache = new Handler\Cache('foo', 'bar');
			$this->assertNull($cache->read($file));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 * @expectedExceptionMessage The cached configuration file "foobar.php" did not return valid configuration data
		 */
		public function testReadCacheWithInvalidData() {
			$file = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$file->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$file->expects($this->once())
				->method('getBasename')
				->willReturn('foobar.php');

			$cache = $this->stubCache->setMethods(array('import'))
				->getMock();

			$cache->expects($this->once())
				->method('import')
				->with($file)
				->willReturn(null);

			$cache->read($file);
		}

		public function testReadCacheWithEmptyData() {
			$file = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$file->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$cache = $this->stubCache->setMethods(array('import'))
				->getMock();

			$cache->expects($this->once())
				->method('import')
				->with($file)
				->willReturn(array());

			$cache->read($file);
		}

		public function testReadCacheWithData() {
			$file = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$file->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$cache = $this->stubCache->setMethods(array('import'))
				->getMock();

			$cache->expects($this->once())
				->method('import')
				->with($file)
				->willReturn(array('foo' => 'bar'));

			$cache->read($file);
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testReadCacheWithoutRegularFile() {
			$file = $this->stubFile->getMock();

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$file->expects($this->once())
				->method('isFile')
				->willReturn(false);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->read($file);
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteDirectoryWithoutPermissions() {
			$directory = $this->stubFile->getMock();
			$file = $this->stubFile->getMock();

			$directory->expects($this->once())
				->method('isDir')
				->willReturn(true);

			$directory->expects($this->once())
				->method('isWritable')
				->willReturn(false);

			$file->expects($this->once())
				->method('getPathInfo')
				->willReturn($directory);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->write($file, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 * @expectedExceptionMessage /Cache directory "[\w\\\/]+" do not exists and can not be created/
		 */
		public function testWriteWithDirectoryCreationFailure() {
			$directory = $this->stubFile->getMock();
			$file = $this->stubFile->getMock();

			$directory->expects($this->once())
				->method('isDir')
				->willReturn(false);

			$directory->expects($this->once())
				->method('isFile')
				->willReturn(false);

			$directory->expects($this->exactly(2))
				->method('getRealPath')
				->willReturn(__DIR__);

			$file->expects($this->once())
				->method('getPathInfo')
				->willReturn($directory);

			$cache = $this->stubCache->setMethods(array('makeDirectory'))
				->getMock();

			$cache->expects($this->once())
				->method('makeDirectory')
				->with(__DIR__, 0777, true)
				->willReturn(false);

			$cache->write($file, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteWithDirectoryAsFile() {
			$directory = $this->stubFile->getMock();
			$file = $this->stubFile->getMock();

			$directory->expects($this->once())
				->method('isDir')
				->willReturn(false);

			$directory->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$file->expects($this->once())
				->method('getPathInfo')
				->willReturn($directory);

			$cache = new Handler\Cache('foo', 'bar');
			$cache->write($file, array());
		}

		public function testWriteWithDirectoryCreation() {
			$directory = $this->stubFile->getMock();
			$file = $this->stubFile->getMock();

			$directory->expects($this->once())
				->method('isDir')
				->willReturn(false);

			$directory->expects($this->once())
				->method('isFile')
				->willReturn(false);

			$directory->expects($this->once())
				->method('getRealPath')
				->willReturn(__FILE__);

			$directory->method('isWritable')
				->willReturn(true);

			$file->expects($this->once())
				->method('getPathInfo')
				->willReturn($directory);

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(false);

			$file->expects($this->once())
				->method('write')
				->with(sprintf('<?php return %s;', var_export(array(), 1)))
				->willReturn(1337);

			$cache = $this->stubCache->setMethods(array('makeDirectory'))
				->getMock();

			$cache->expects($this->once())
				->method('makeDirectory')
				->with(__FILE__, 0777, true)
				->willReturn(true);

			$this->assertTrue($cache->write($file, array()));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteToCacheFileWithoutFile() {
			$directory = $this->stubFile->getMock();
			$file = $this->stubFile->getMock();

			$directory->expects($this->once())
				->method('isDir')
				->willReturn(true);

			$directory->expects($this->once())
				->method('isWritable')
				->willReturn(true);

			$file->expects($this->once())
				->method('getPathInfo')
				->willReturn($directory);

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$file->expects($this->once())
				->method('isFile')
				->willReturn(false);

			$cache = new Handler\Cache('foo', 'bar');
			$cache->write($file, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteToCacheFileWithoutPermissions() {
			$directory = $this->stubFile->getMock();
			$file = $this->stubFile->getMock();

			$directory->expects($this->once())
				->method('isDir')
				->willReturn(true);

			$directory->expects($this->once())
				->method('isWritable')
				->willReturn(true);

			$file->expects($this->once())
				->method('getPathInfo')
				->willReturn($directory);

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$file->expects($this->once())
				->method('isFile')
				->willReturn(true);

			$file->expects($this->once())
				->method('isWritable')
				->willReturn(false);

			$cache = new Handler\Cache('foo', 'bar');
			$cache->write($file, array());
		}

		public function testWrite() {
			$directory = $this->stubFile->getMock();
			$file = $this->stubFile->getMock();

			$directory->expects($this->once())
				->method('isDir')
				->willReturn(true);

			$directory->expects($this->once())
				->method('isWritable')
				->willReturn(true);

			$file->expects($this->once())
				->method('getPathInfo')
				->willReturn($directory);

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(false);

			$file->expects($this->once())
				->method('write')
				->with(sprintf('<?php return %s;', var_export(array('baz' => 'qux'), 1)))
				->willReturn(1337);

			$cache = new Handler\Cache('foo', 'bar');
			$this->assertTrue($cache->write($file, array('baz' => 'qux')));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteFailed() {
			$directory = $this->stubFile->getMock();
			$file = $this->stubFile->getMock();

			$directory->expects($this->once())
				->method('isDir')
				->willReturn(true);

			$directory->expects($this->once())
				->method('isWritable')
				->willReturn(true);

			$file->expects($this->once())
				->method('getPathInfo')
				->willReturn($directory);

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(false);

			$file->expects($this->once())
				->method('write')
				->with(sprintf('<?php return %s;', var_export(array(), 1)))
				->willReturn(null);

			$file->expects($this->once())
				->method('getBasename')
				->willReturn('cache');

			$cache = new Handler\Cache('foo', 'bar');
			$cache->write($file, array());
		}
	}
}
// End of file: Cache.php
// Location: test/library/configuration/handler/Cache.php
