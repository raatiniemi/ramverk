<?php
namespace Me\Raatiniemi\Ramverk\Test {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Configuration as Config;

	/**
	 * Unit test case for the configuration data container.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Configuration extends \PHPUnit_Framework_TestCase {
		// Set

		public function testSet() {
			$config = new Config();
			$this->assertTrue($config->set('foo', 'bar'));
		}

		public function testSetAlreadyExisting() {
			$config = new Config();
			$config->set('foo', 'bar');

			$this->assertFalse($config->set('foo', 'baz'));
			$this->assertEquals($config->get('foo'), 'bar');
		}

		public function testSetOverride() {
			$config = new Config();
			$config->set('foo', 'bar');

			$this->assertTrue($config->set('foo', 'baz', true));
			$this->assertEquals($config->get('foo'), 'baz');
		}

		/**
		 * @expectedException Me\Raatiniemi\Ramverk\Exception
		 */
		public function testSetReadonly() {
			$config = new Config();
			$config->set('foo', 'bar', false, true);
			$config->set('foo', 'bar', true);
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testSetWithArrayAsName() {
			$config = new Config();
			$config->set(array(), 'foo');
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testSetWithBooleanAsName() {
			$config = new Config();
			$config->set(true, 'foo');
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testSetWithIntegerAsName() {
			$config = new Config();
			$config->set(1337, 'foo');
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testSetWithDoubleAsName() {
			$config = new Config();
			$config->set(13.37, 'foo');
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testSetWithObjectAsName() {
			$config = new Config();
			$config->set(new \stdClass(), 'foo');
		}

		// Get

		public function testGet() {
			$config = new Config();
			$this->assertTrue($config->set('foo', 'bar'));
			$this->assertEquals($config->get('foo'), 'bar');
		}

		public function testGetNonExisting() {
			$config = new Config();
			$this->assertNull($config->get('foo'));
		}

		public function testGetDefault() {
			$config = new Config();
			$this->assertEquals($config->get('foo', 'bar'), 'bar');
		}

		// Has

		public function testHas() {
			$config = new Config();
			$config->set('foo', 'bar');
			$this->assertTrue($config->has('foo'));
			$this->assertEquals($config->get('foo'), 'bar');
		}

		public function testHasNot() {
			$config = new Config();
			$this->assertFalse($config->has('foo'));
			$this->assertNull($config->get('foo'));
		}

		public function testHasReadonly() {
			$config = new Config();
			$this->assertTrue($config->set('foo', 'bar', false, true));
			$this->assertTrue($config->hasReadonly('foo'));
		}

		public function testHasNotReadonly() {
			$config = new Config();
			$this->assertFalse($config->hasReadonly('foo'));
			$this->assertNull($config->get('foo'));
		}

		// From array

		public function testFromArray() {
			$config = new Config();
			$this->assertTrue($config->fromArray(array('foo' => 'bar')));
			$this->assertEquals($config->get('foo'), 'bar');
		}

		public function testFromArrayWithAlreadyExisting() {
			$config = new Config();
			$this->assertTrue($config->set('foo', 'bar'));
			$this->assertFalse($config->fromArray(array('foo' => 'baz')));
			$this->assertEquals($config->get('foo'), 'bar');
		}

		public function testFromArrayWithOverride() {
			$config = new Config();
			$this->assertTrue($config->set('foo', 'bar'));
			$this->assertTrue($config->fromArray(array('foo' => 'baz'), true));
			$this->assertEquals($config->get('foo'), 'baz');
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testFromArrayWithBooleanAsName() {
			$config = new Config();
			$config->fromArray(array(true => 'foo'));
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testFromArrayWithIntegerAsName() {
			$config = new Config();
			$config->fromArray(array(1337 => 'foo'));
		}

		/**
		 * @expectedException InvalidArgumentException
		 */
		public function testFromArrayWithDoubleAsName() {
			$config = new Config();
			$config->fromArray(array(13.37 => 'foo'));
		}

		// To array

		public function testExport() {
			$data = array('foo' => 'bar', 'baz' => 'qux');
			$config = new Config();
			$config->fromArray($data);
			$this->assertEquals($config->toArray(), $data);
		}

		public function testExportEmpty() {
			$config = new Config();
			$this->assertEquals($config->toArray(), array());
		}
	}
}
// End of file: Configuration.php
// Location: test/library/configuration/Configuration.php