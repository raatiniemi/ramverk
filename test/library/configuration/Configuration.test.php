<?php
namespace Me\Raatiniemi\Ramverk\Test
{
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
	class Configuration extends \UnitTestCase
	{
		// Set

		public function testSet()
		{
			$config = new Config();
			$this->assertTrue($config->set('foo', 'bar'));
		}

		public function testSetAlreadyExisting()
		{
			$config = new Config();
			$config->set('foo', 'bar');

			$this->assertFalse($config->set('foo', 'baz'));
			$this->assertEqual($config->get('foo'), 'bar');
		}

		public function testSetOverride()
		{
			$config = new Config();
			$config->set('foo', 'bar');

			$this->assertTrue($config->set('foo', 'baz', true));
			$this->assertEqual($config->get('foo'), 'baz');
		}

		public function testSetReadonly()
		{
			$config = new Config();
			$config->set('foo', 'bar', false, true);

			$this->expectException();
			$config->set('foo', 'bar', true);
		}

		public function testSetWithArrayAsName()
		{
			$config = new Config();

			$this->expectException();
			$config->set(array(), 'foo');
		}

		public function testSetWithBooleanAsName()
		{
			$config = new Config();

			$this->expectException();
			$config->set(true, 'foo');
		}

		public function testSetWithIntegerAsName()
		{
			$config = new Config();

			$this->expectException();
			$config->set(1337, 'foo');
		}

		public function testSetWithDoubleAsName()
		{
			$config = new Config();

			$this->expectException();
			$config->set(13.37, 'foo');
		}

		public function testSetWithObjectAsName()
		{
			$config = new Config();

			$this->expectException();
			$config->set(new \stdClass(), 'foo');
		}

		// Get

		public function testGet()
		{
			$config = new Config();

			$this->assertTrue($config->set('foo', 'bar'));
			$this->assertEqual($config->get('foo'), 'bar');
		}

		public function testGetNonExisting()
		{
			$config = new Config();

			$this->assertNull($config->get('foo'));
		}

		public function testGetDefault()
		{
			$config = new Config();

			$this->assertEqual($config->get('foo', 'bar'), 'bar');
		}

		// Has

		public function testHas()
		{
			$config = new Config();
			$config->set('foo', 'bar');

			$this->assertTrue($config->has('foo'));
			$this->assertEqual($config->get('foo'), 'bar');
		}

		public function testHasNot()
		{
			$config = new Config();

			$this->assertFalse($config->has('foo'));
			$this->assertNull($config->get('foo'));
		}

		public function testHasReadonly()
		{
			$config = new Config();

			$this->assertTrue($config->set('foo', 'bar', false, true));
			$this->assertTrue($config->hasReadonly('foo'));
		}

		public function testHasNotReadonly()
		{
			$config = new Config();

			$this->assertFalse($config->hasReadonly('foo'));
			$this->assertNull($config->get('foo'));
		}

		// From array

		public function testFromArray()
		{
			$config = new Config();

			$this->assertTrue($config->fromArray(array('foo' => 'bar')));
			$this->assertEqual($config->get('foo'), 'bar');
		}

		public function testFromArrayWithAlreadyExisting()
		{
			$config = new Config();

			$this->assertTrue($config->set('foo', 'bar'));
			$this->assertFalse($config->fromArray(array('foo' => 'baz')));
			$this->assertEqual($config->get('foo'), 'bar');
		}

		public function testFromArrayWithOverride()
		{
			$config = new Config();

			$this->assertTrue($config->set('foo', 'bar'));
			$this->assertTrue($config->fromArray(array('foo' => 'baz'), true));
			$this->assertEqual($config->get('foo'), 'baz');
		}

		public function testFromArrayWithBooleanAsName()
		{
			$config = new Config();

			$this->expectException();
			$config->fromArray(array(true => 'foo'));
		}

		public function testFromArrayWithIntegerAsName()
		{
			$config = new Config();

			$this->expectException();
			$config->fromArray(array(1337 => 'foo'));
		}

		public function testFromArrayWithDoubleAsName()
		{
			$config = new Config();

			$this->expectException();
			$config->fromArray(array(13.37 => 'foo'));
		}

		// To array

		public function testExport()
		{
			$data = array('foo' => 'bar', 'baz' => 'qux');
			$config = new Config();
			$config->fromArray($data);

			$this->assertEqual($config->toArray(), $data);
		}

		public function testExportEmpty()
		{
			$config = new Config();

			$this->assertEqual($config->toArray(), array());
		}
	}
}
// End of file: Configuration.test.php
// Location: test/library/configuration/Configuration.test.php