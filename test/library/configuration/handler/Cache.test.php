<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler
{
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
	class Cache extends \UnitTestCase
	{
		private $directory = '/tmp/ramverk';

		private function clearDirectory()
		{
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

		public function setUp()
		{
			$this->clearDirectory();
		}

		public function tearDown()
		{
			$this->clearDirectory();
		}

		public function testInitializeWithArrayAsProfile()
		{
			$this->expectException();
			new Handler\Cache(array(), null);
		}

		public function testInitializeWithIntegerAsProfile()
		{
			$this->expectException();
			new Handler\Cache(1337, null);
		}

		public function testInitializeWithDoubleAsProfile()
		{
			$this->expectException();
			new Handler\Cache(13.37, null);
		}

		public function testInitializeWithObjectAsProfile()
		{
			$this->expectException();
			new Handler\Cache(new \stdClass, null);
		}

		public function testInitializeWithNullAsProfile()
		{
			$this->expectException();
			new Handler\Cache(null, null);
		}

		public function testInitializeWithEmptyProfile()
		{
			$this->expectException();
			new Handler\Cache('', null);
		}

		public function testInitializeWithArrayAsContext()
		{
			$this->expectException();
			new Handler\Cache('foo', array());
		}

		public function testInitializeWithIntegerAsContext()
		{
			$this->expectException();
			new Handler\Cache('foo', 1337);
		}

		public function testInitializeWithDoubleAsContext()
		{
			$this->expectException();
			new Handler\Cache('foo', 13.37);
		}

		public function testInitializeWithObjectAsContext()
		{
			$this->expectException();
			new Handler\Cache('foo', new \stdClass);
		}

		public function testInitializeWithNullAsContext()
		{
			$this->expectException();
			new Handler\Cache('foo', null);
		}

		public function testInitializeWithEmptyContext()
		{
			$this->expectException();
			new Handler\Cache('foo', '');
		}

		// Generate name

		public function testGenerateName()
		{
			$cache = new Handler\Cache('foo', 'bar');

			$this->assertEqual(
				$cache->generateName('/baz/qux.xml'),
				'qux.xml_foo_bar_8b3287a42642ee9dd7f4b5b4fd4c8cef18993a43.php'
			);
		}

		// Is modified

		public function testIsModifiedWithEmptyFilename()
		{
			$cache = new Handler\Cache('foo', 'bar');

			$this->expectException();
			$cache->isModified('', 'baz');
		}

		public function testIsModifiedWithEmptyCachename()
		{
			$cache = new Handler\Cache('foo', 'bar');

			$this->expectException();
			$cache->isModified(__FILE__, '');
		}

		public function testIsModifiedWithNonExistingFilename()
		{
			$cache = new Handler\Cache('foo', 'bar');

			$this->expectException();
			$cache->isModified('baz', 'qux');
		}

		public function testIsModifiedWithoutCache()
		{
			$cache = new Handler\Cache('foo', 'bar');

			$file = "{$this->directory}/config";
			file_put_contents($file, '');

			$this->assertTrue($cache->isModified($file, '/tmp/foo'));
		}

		public function testIsModifiedWithOldCache()
		{
			$cache = new Handler\Cache('foo', 'bar');

			$file['cache'] = "{$this->directory}/cache";
			$file['config'] = "{$this->directory}/config";

			file_put_contents($file['cache'], '');
			sleep(1);
			file_put_contents($file['config'], '');

			clearstatcache();
			$this->assertTrue($cache->isModified($file['config'], $file['cache']));
		}

		public function testIsModifiedWithNewCache()
		{
			$cache = new Handler\Cache('foo', 'bar');

			$file['cache'] = "{$this->directory}/cache";
			$file['config'] = "{$this->directory}/config";

			file_put_contents($file['config'], '');
			sleep(1);
			file_put_contents($file['cache'], '');

			clearstatcache();
			$this->assertFalse($cache->isModified($file['config'], $file['cache']));
		}
	}
}
// End of file: Cache.test.php
// Location: test/library/configuration/handler/Cache.test.php