<?php
namespace Me\Raatiniemi\Ramverk\Test\Utility {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class File extends \PHPUnit_Framework_TestCase {
		private $class = 'Me\\Raatiniemi\\Ramverk\\Utility\\File';

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testReadWithoutPermissions() {
			$file = $this->getMock($this->class, array('isReadable'), array(__FILE__));
			$file->method('isReadable')->willReturn(false);

			$file->read();
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testReadWithFailure() {
			$file = $this->getMock($this->class, array('isReadable', 'eof', 'fgets'), array(__FILE__));
			$file->method('isReadable')->willReturn(true);
			$file->method('eof')->willReturn(false);
			$file->method('fgets')->willReturn(false);

			$file->read();
		}

		public function testReadSingleLine() {
			$file = $this->getMock($this->class, array('isReadable', 'eof', 'fgets'), array(__FILE__));
			$file->method('isReadable')->willReturn(true);
			$file->method('eof')->will($this->onConsecutiveCalls(false, true));
			$file->method('fgets')->willReturn('foo');

			$this->assertEquals('foo', $file->read());
		}

		public function testReadMultipleLines() {
			$file = $this->getMock($this->class, array('isReadable', 'eof', 'fgets'), array(__FILE__));
			$file->method('isReadable')->willReturn(true);
			$file->method('eof')->will($this->onConsecutiveCalls(false, false, true));
			$file->method('fgets')->willReturn('foo');

			$this->assertEquals('foo'.PHP_EOL.'foo', $file->read());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteWithoutPermissions() {
			$file = $this->getMock($this->class, array('isWritable'), array(__FILE__));
			$file->method('isWritable')->willReturn(false);

			$file->write('foo');
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testWriteWithFailure() {
			$file = $this->getMock($this->class, array('isWritable', 'fwrite'), array(__FILE__));
			$file->method('isWritable')->willReturn(true);
			$file->method('fwrite')->willReturn(null);

			$file->write('foo');
		}

		public function testWrite() {
			$file = $this->getMock($this->class, array('isWritable', 'fwrite'), array(__FILE__));
			$file->method('isWritable')->willReturn(true);
			$file->method('fwrite')->willReturn(1337);

			$this->assertEquals(1337, $file->write('foo'));
		}
	}
}
// End of file: File.test.php
// Location: test/library/utility/File.test.php