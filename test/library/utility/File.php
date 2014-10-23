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
		private $stub;

		public function setUp() {
			$this->stub = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Utility\\File');
		}

		public function tearDown() {
			$this->stub = null;
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 * @expectedExceptionMessage
		 */
		public function testReadWithoutPermissions() {
			$file = $this->stub->setConstructorArgs(array(__FILE__))
				->setMethods(array('isReadable'))
				->getMock();

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(false);

			$file->read();
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 * @expectedExceptionMessage
		 */
		public function testReadWithFailure() {
			$file = $this->stub->setConstructorArgs(array(__FILE__))
				->setMethods(array('isReadable', 'eof', 'fgets'))
				->getMock();

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$file->expects($this->once())
				->method('eof')
				->willReturn(false);

			$file->expects($this->once())
				->method('fgets')
				->willReturn(false);

			$file->read();
		}

		public function testReadSingleLine() {
			$file = $this->stub->setConstructorArgs(array(__FILE__))
				->setMethods(array('isReadable', 'eof', 'fgets'))
				->getMock();

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$file->expects($this->exactly(2))
				->method('eof')
				->will($this->onConsecutiveCalls(false, true));

			$file->expects($this->once())
				->method('fgets')
				->willReturn('foo');

			$this->assertEquals('foo', $file->read());
		}

		public function testReadMultipleLines() {
			$file = $this->stub->setConstructorArgs(array(__FILE__))
				->setMethods(array('isReadable', 'eof', 'fgets'))
				->getMock();

			$file->expects($this->once())
				->method('isReadable')
				->willReturn(true);

			$file->expects($this->exactly(3))
				->method('eof')
				->will($this->onConsecutiveCalls(false, false, true));

			$file->expects($this->exactly(2))
				->method('fgets')
				->willReturn('foo');

			$this->assertEquals(implode(PHP_EOL, array('foo', 'foo')), $file->read());
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 * @expectedExceptionMessage
		 */
		public function testWriteWithoutPermissions() {
			$file = $this->stub->setConstructorArgs(array(__FILE__))
				->setMethods(array('isWritable'))
				->getMock();

			$file->expects($this->once())
				->method('isWritable')
				->willReturn(false);

			$file->write('foo');
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 * @expectedExceptionMessage
		 */
		public function testWriteWithFailure() {
			$file = $this->stub->setConstructorArgs(array(__FILE__))
				->setMethods(array('isWritable', 'fwrite'))
				->getMock();

			$file->expects($this->once())
				->method('isWritable')
				->willReturn(true);

			$file->expects($this->once())
				->method('fwrite')
				->with('foo')
				->willReturn(null);

			$file->write('foo');
		}

		public function testWrite() {
			$file = $this->stub->setConstructorArgs(array(__FILE__))
				->setMethods(array('isWritable', 'fwrite'))
				->getMock();

			$file->expects($this->once())
				->method('isWritable')
				->willReturn(true);

			$file->expects($this->once())
				->method('fwrite')
				->with('foo')
				->willReturn(1337);

			$this->assertEquals(1337, $file->write('foo'));
		}
	}
}
// End of file: File.php
// Location: test/library/utility/File.php