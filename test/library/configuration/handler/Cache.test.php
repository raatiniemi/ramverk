<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Configuration\Handler;

	/**
	 * Unit test case for the core configuration handler.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class CacheTest extends \PHPUnit_Framework_TestCase {
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
			$cache = new Handler\Cache('foo', 'bar');
			$this->assertEquals(
				$cache->generateName(new \SplFileInfo('/baz/qux.xml')),
				'qux.xml_foo_bar_8b3287a42642ee9dd7f4b5b4fd4c8cef18993a43.php'
			);
		}

		public function testGenerateNameFailed() {
			$cache = new Handler\Cache('foo', 'bar');
			$this->assertNotEquals(
				$cache->generateName(new \SplFileInfo('baz')),
				'qux.xml_foo_bar_8b3287a42642ee9dd7f4b5b4fd4c8cef18993a43.php'
			);
		}

		/**
		 * @expectedException PHPUnit_Framework_Error
		 */
		public function testGenerateNameWithoutFilename() {
			$cache = new Handler\Cache('foo', 'bar');
			$cache->generateName(null);
		}

		// Is modified

		/**
		 * @expectedException PHPUnit_Framework_Error
		 */
		public function testIsModifiedWithoutConfigurationFile() {
			$cache = new Handler\Cache('foo', 'bar');
			$cache->isModified(null, null);
		}

		/**
		 * @expectedException PHPUnit_Framework_Error
		 */
		public function testIsModifiedWithoutCacheFile() {
			$cache = new Handler\Cache('foo', 'bar');
			$filename = new \SplFileInfo(__FILE__);
			$cache->isModified($filename, null);
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testIsModifiedWithoutExistingConfigurationFile() {
			$cache = new Handler\Cache('foo', 'bar');
			$filename = new \SplFileInfo('baz');
			$cachename = new \SplFileInfo('qux');
			$cache->isModified($filename, $cachename);
		}

		public function testIsModifiedWithoutExistingCacheFile() {
			$cache = new Handler\Cache('foo', 'bar');
			$filename = new \SplFileInfo(__FILE__);
			$cachename = new \SplFileInfo('bar');

			$this->assertTrue($cache->isModified($filename, $cachename));
		}

		public function testIsModifiedWithOldCacheFile() {
			$cache = new Handler\Cache('foo', 'bar');

			$builder = $this->getMockBuilder('SplFileInfo');

			$filename = $builder->setConstructorArgs(array('config'))->getMock();
			$filename->method('isFile')->willReturn(true);
			$filename->method('isReadable')->willReturn(true);
			$filename->method('getMTime')->willReturn(time() + 1);

			$cachename = $builder->setConstructorArgs(array('cache'))->getMock();
			$cachename->method('isFile')->willReturn(true);
			$cachename->method('isReadable')->willReturn(true);
			$cachename->method('getMTime')->willReturn(time());

			$this->assertTrue($cache->isModified($filename, $cachename));
		}

		public function testIsModifiedWithNewCache() {
			$cache = new Handler\Cache('foo', 'bar');

			$builder = $this->getMockBuilder('SplFileInfo');

			$filename = $builder->setConstructorArgs(array('config'))->getMock();
			$filename->method('isFile')->willReturn(true);
			$filename->method('isReadable')->willReturn(true);
			$filename->method('getMTime')->willReturn(time());

			$cachename = $builder->setConstructorArgs(array('cache'))->getMock();
			$cachename->method('isFile')->willReturn(true);
			$cachename->method('isReadable')->willReturn(true);
			$cachename->method('getMTime')->willReturn(time() + 1);

			$this->assertFalse($cache->isModified($filename, $cachename));
		}

		// Read

		public function testReadNonExistingCache() {
			$cache = new Handler\Cache('foo', 'bar');
			$cachename = new \SplFileInfo('baz');

			$this->assertNull($cache->read($cachename));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testReadEmptyCache() {
			$cache = new Handler\Cache('foo', 'bar');
			$cachename = new \SplFileInfo("{$this->directory}/cache");

			$this->assertTrue(file_put_contents($cachename->getPathname(), '') !== false);
			$cache->read($cachename);
		}

		public function testReadEmptyConfigurationCache() {
			$cache = new Handler\Cache('foo', 'bar');
			$cachename = new \SplFileInfo("{$this->directory}/cache");

			$data = array();
			$content = sprintf('<?php return %s;', var_export($data, 1));
			$this->assertTrue(file_put_contents($cachename->getPathname(), $content) !== false);
			$this->assertEquals($data, $cache->read($cachename));
		}

		public function testReadCache() {
			$cache = new Handler\Cache('foo', 'bar');
			$cachename = new \SplFileInfo("{$this->directory}/cache");

			$data = array('baz' => 'qux');
			$content = sprintf('<?php return %s;', var_export($data, 1));
			$this->assertTrue(file_put_contents($cachename->getPathname(), $content) !== false);
			$this->assertEquals($data, $cache->read($cachename));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testReadCacheWithDirectory() {
			$cache = new Handler\Cache('foo', 'bar');
			$cachename = new \SplFileInfo($this->directory);

			$cache->read($cachename);
		}

		// Write

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteToNonExistingDirectoryWithoutPermissions() {
			$cache = new Handler\Cache('foo', 'bar');

			$builder = $this->getMockBuilder('SplFileInfo');

			$directory = $builder->setConstructorArgs(array('directory'))->getMock();
			$directory->method('isDir')->willReturn(false);
			$directory->method('isWritable')->willReturn(false);

			$cachename = $builder->setConstructorArgs(array('cache'))->getMock();
			$cachename->method('getPathInfo')->willReturn($directory);

			$cache->write($cachename, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteToDirectoryWithoutPermissions() {
			$cache = new Handler\Cache('foo', 'bar');

			$builder = $this->getMockBuilder('SplFileInfo');

			$directory = $builder->setConstructorArgs(array('directory'))->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(false);

			$cachename = $builder->setConstructorArgs(array('cache'))->getMock();
			$cachename->method('getPathInfo')->willReturn($directory);

			$cache->write($cachename, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteToCacheFileWithoutFile() {
			$cache = new Handler\Cache('foo', 'bar');

			$builder = $this->getMockBuilder('SplFileInfo');

			$directory = $builder->setConstructorArgs(array('directory'))->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(true);

			$cachename = $builder->setConstructorArgs(array('cache'))->getMock();
			$cachename->method('getPathInfo')->willReturn($directory);
			$cachename->method('isReadable')->willReturn(true);
			$cachename->method('isFile')->willReturn(false);

			$cache->write($cachename, array());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteToCacheFileWithoutPermissions() {
			$cache = new Handler\Cache('foo', 'bar');

			$builder = $this->getMockBuilder('SplFileInfo');

			$directory = $builder->setConstructorArgs(array('directory'))->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(true);

			$cachename = $builder->setConstructorArgs(array('cache'))->getMock();
			$cachename->method('getPathInfo')->willReturn($directory);
			$cachename->method('isReadable')->willReturn(true);
			$cachename->method('isFile')->willReturn(true);
			$cachename->method('isWritable')->willReturn(false);

			$cache->write($cachename, array());
		}

		public function testWrite() {
			$cache = new Handler\Cache('foo', 'bar');

			$fileBuilder = $this->getMockBuilder('SplFileInfo');

			$directory = $fileBuilder->setConstructorArgs(array('directory'))->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(true);

			$objectBuilder = $this->getMockBuilder('SplFileObject');
			$file = $objectBuilder->setConstructorArgs(array(__FILE__))->getMock();
			$file->method('fwrite')->willReturn(1337);

			$cachename = $fileBuilder->setConstructorArgs(array('cache'))->getMock();
			$cachename->method('getPathInfo')->willReturn($directory);
			$cachename->method('isReadable')->willReturn(false);
			$cachename->method('openFile')->willReturn($file);

			$this->assertTrue($cache->write($cachename, array('baz' => 'qux')));
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteFailed() {
			$cache = new Handler\Cache('foo', 'bar');

			$fileBuilder = $this->getMockBuilder('SplFileInfo');

			$directory = $fileBuilder->setConstructorArgs(array('directory'))->getMock();
			$directory->method('isDir')->willReturn(true);
			$directory->method('isWritable')->willReturn(true);

			$objectBuilder = $this->getMockBuilder('SplFileObject');
			$file = $objectBuilder->setConstructorArgs(array(__FILE__))->getMock();
			$file->method('fwrite')->willReturn(null);

			$cachename = $fileBuilder->setConstructorArgs(array('directory'))->getMock();
			$cachename->method('getPathInfo')->willReturn($directory);
			$cachename->method('isReadable')->willReturn(true);
			$cachename->method('isFile')->willReturn(true);
			$cachename->method('isWritable')->willReturn(true);
			$cachename->method('openFile')->willReturn($file);
			$cachename->method('getBasename')->willReturn('cache');

			$this->assertTrue($cache->write($cachename, array('baz' => 'qux')));
		}
	}
}
// End of file: Cache.test.php
// Location: test/library/configuration/handler/Cache.test.php