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
		private $file = 'Me\\Raatiniemi\\Ramverk\\Utility\\File';

		private $cache = 'Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Cache';

		private $directory = '/tmp/ramverk';

		private function clearDirectory() {
			if(is_dir($this->directory)) {
				$directory = new \DirectoryIterator($this->directory);
				foreach($directory as $info) {
					if($info->isDot()) {
						continue;
					}

					if($info->isDir()) {
						throw new \Exception(sprintf(
							'Directory located within the tmp-folder, "%s"',
							$info->getPathname()
						));
					}

					if(!unlink($info->getPathname())) {
						throw new \Exception(sprintf(
							'Unable to remove file from tmp-folder, "%s"',
							$info->getPathname()
						));
					}
				}

				if(!rmdir($this->directory)) {
					throw new \Exception(sprintf(
						'Unable to remove directory "%s"',
						$this->directory
					));
				}
			}

			if(!mkdir($this->directory, 0777)) {
				throw new \Exception(sprintf(
					'Unable to create directory "%s"',
					$this->directory
				));
			}
		}

		public function setUp() {
			$this->clearDirectory();
		}

		public function tearDown() {
			$this->clearDirectory();
		}

		// Initialize

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

		// Generate name

		public function testGenerateName() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('getBasename')->willReturn('qux.xml');
			$file->method('getPathname')->willReturn('/baz/qux.xml');

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertEquals(
				$handler->generateName($file),
				'qux.xml_foo_bar_8b3287a42642ee9dd7f4b5b4fd4c8cef18993a43.php'
			);
		}

		public function testGenerateNameFailed() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('getBasename')->willReturn('qux.xml');
			$file->method('getPathname')->willReturn('qux.xml');

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertNotEquals(
				$handler->generateName($file),
				'qux.xml_foo_bar_8b3287a42642ee9dd7f4b5b4fd4c8cef18993a43.php'
			);
		}

		/**
		 * @expectedException PHPUnit_Framework_Error
		 */
		public function testGenerateNameWithoutFilename() {
			$handler = new Handler\Cache('foo', 'bar');
			$handler->generateName(null);
		}

		// Is modified

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
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('isFile')->willReturn(false);

			$cache = $stub->getMock();

			$handler = new Handler\Cache('foo', 'bar');
			$handler->isModified($file, $cache);
		}

		public function testIsModifiedWithoutExistingCacheFile() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('isFile')->willReturn(true);
			$file->method('isReadable')->willReturn(true);

			$cache = $stub->getMock();
			$cache->method('isReadable')->willReturn(false);

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertTrue($handler->isModified($file, $cache));
		}

		public function testIsModifiedWithOldCacheFile() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('isFile')->willReturn(true);
			$file->method('isReadable')->willReturn(true);
			$file->method('getMTime')->willReturn(time() + 1);

			$cache = $stub->getMock();
			$cache->method('isFile')->willReturn(true);
			$cache->method('isReadable')->willReturn(true);
			$cache->method('getMTime')->willReturn(time());

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertTrue($handler->isModified($file, $cache));
		}

		public function testIsModifiedWithNewCache() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('isFile')->willReturn(true);
			$file->method('isReadable')->willReturn(true);
			$file->method('getMTime')->willReturn(time());

			$cache = $stub->getMock();
			$cache->method('isReadable')->willReturn(true);
			$cache->method('isFile')->willReturn(true);
			$cache->method('getMTime')->willReturn(time() + 1);

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertFalse($handler->isModified($file, $cache));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testIsModifiedWithoutRegularCachefile() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('isFile')->willReturn(true);
			$file->method('isReadable')->willReturn(true);

			$cache = $stub->getMock();
			$cache->method('isReadable')->willReturn(true);
			$cache->method('isFile')->willReturn(false);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->isModified($file, $cache);
		}

		// Read

		public function testReadNonExistingCache() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('isReadable')->willReturn(false);

			$cache = new Handler\Cache('foo', 'bar');
			$this->assertNull($cache->read($file));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testReadCacheWithInvalidData() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('isReadable')->willReturn(true);

			$stub = $this->getMockBuilder($this->cache)
				->disableOriginalConstructor()
				->setMethods(array('import'));

			$cache = $stub->getMock();
			$cache->method('import')->willReturn(null);

			$cache->read($file);
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testReadCacheWithoutRegularFile() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$file = $stub->getMock();
			$file->method('isReadable')->willReturn(true);
			$file->method('isFile')->willReturn(false);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->read($file);
		}

		// Write

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteDirectoryWithoutPermissions() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$directory = $stub->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(false);

			$file = $stub->getMock();
			$file->method('getPathInfo')->willReturn($directory);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->write($file, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteWithDirectoryCreationFailure() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$directory = $stub->getMock();
			$directory->method('isDir')->willReturn(false);
			$directory->method('isWritable')->willReturn(false);

			$file = $stub->getMock();
			$file->method('getPathInfo')->willReturn($directory);

			$stub = $this->getMockBuilder($this->cache)
				->disableOriginalConstructor()
				->setMethods(array('mkdir'));

			$handler = $stub->getMock();
			$handler->method('mkdir')->willReturn(false);

			$handler->write($file, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteWithDirectoryAsFile() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$directory = $stub->getMock();
			$directory->method('isDir')->willReturn(false);
			$directory->method('isFile')->willReturn(true);

			$file = $stub->getMock();
			$file->method('getPathInfo')->willReturn($directory);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->write($file, array());
		}

		public function testWriteWithDirectoryCreation() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$directory = $stub->getMock();
			$directory->method('isDir')->willReturn(false);
			$directory->method('isFile')->willReturn(false);
			$directory->method('isWritable')->willReturn(true);

			$file = $stub->getMock();
			$file->method('getPathInfo')->willReturn($directory);
			$file->method('write')->willReturn(1337);

			$stub = $this->getMockBuilder($this->cache)
				->disableOriginalConstructor()
				->setMethods(array('mkdir'));

			$handler = $stub->getMock();
			$handler->method('mkdir')->willReturn(true);

			$handler->write($file, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteToCacheFileWithoutFile() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$directory = $stub->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(true);

			$file = $stub->getMock();
			$file->method('getPathInfo')->willReturn($directory);
			$file->method('isReadable')->willReturn(true);
			$file->method('isFile')->willReturn(false);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->write($file, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteToCacheFileWithoutPermissions() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$directory = $stub->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(true);

			$file = $stub->getMock();
			$file->method('getPathInfo')->willReturn($directory);
			$file->method('isReadable')->willReturn(true);
			$file->method('isFile')->willReturn(true);
			$file->method('isWritable')->willReturn(false);

			$handler = new Handler\Cache('foo', 'bar');
			$handler->write($file, array());
		}

		public function testWrite() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$directory = $stub->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(true);

			$file = $stub->getMock();
			$file->method('getPathInfo')->willReturn($directory);
			$file->method('isReadable')->willReturn(false);
			$file->method('write')->willReturn(1337);

			$handler = new Handler\Cache('foo', 'bar');
			$this->assertTrue($handler->write($file, array('baz' => 'qux')));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteFailed() {
			$stub = $this->getMockBuilder($this->file)
				->setConstructorArgs(array(__FILE__));

			$directory = $stub->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(true);

			$file = $stub->getMock();
			$file->method('getPathInfo')->willReturn($directory);
			$file->method('isReadable')->willReturn(false);
			$file->method('write')->willReturn(null);
			$file->method('getBasename')->willReturn('cache');

			$handler = new Handler\Cache('foo', 'bar');
			$handler->write($file, array());
		}
	}
}
// End of file: Cache.test.php
// Location: test/library/configuration/handler/Cache.test.php