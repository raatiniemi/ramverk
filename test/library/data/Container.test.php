<?php
namespace Me\Raatiniemi\Ramverk\Test\Data
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Data;

	/**
	 * Unit test case for the data container.
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
			$container = new Data\Container();
			$this->assertTrue($container->set('foo', 'bar'));
		}

		public function testSetFailed()
		{
			$container = new Data\Container();
			$container->set('foo', 'bar');
			$this->assertFalse($container->set('foo', 'baz'));
		}

		public function testSetOverride()
		{
			$container = new Data\Container();
			$container->set('foo', 'bar');
			$this->assertTrue($container->set('foo', 'baz', TRUE));
		}
	}
}
// End of file: Container.test.php
// Location: test/library/data/Container.test.php