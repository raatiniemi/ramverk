<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Configuration;

	/**
	 * Unit test case for the configuration data container.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Container extends \UnitTestCase
	{
		public function testSet()
		{
			$container = new Configuration\Container();
			$this->assertTrue($container->set('foo', 'bar'));
		}

		public function testSetReadonly()
		{
			$container = new Configuration\Container();
			$container->set('foo', 'bar', FALSE, TRUE);
			$this->expectException();
			$container->set('foo', 'bar', TRUE);
		}

		public function testGet()
		{
			$container = new Configuration\Container();
			$container->set('foo', 'bar');
			$this->assertEqual($container->get('foo'), 'bar');
		}

		public function testGetDefault()
		{
			$container = new Configuration\Container();
			$this->assertEqual($container->get('foo', 'bar'), 'bar');
		}

		public function testHasReadonly()
		{
			$container = new Configuration\Container();
			$container->set('foo', 'bar', FALSE, TRUE);
			$this->assertTrue($container->hasReadonly('foo'));
		}

		public function testHasNotReadonly()
		{
			$container = new Configuration\Container();
			$this->assertFalse($container->hasReadonly('foo'));
		}
	}
}
// End of file: Container.test.php
// Location: test/library/configuration/Container.test.php