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

		public function testGet()
		{
			$container = new Data\Container();
			$container->set('foo', 'bar');
			$this->assertEqual($container->get('foo'), 'bar');
		}

		public function testGetFailed()
		{
			$container = new Data\Container();
			$this->assertNull($container->get('foo'));
		}

		public function testHas()
		{
			$container = new Data\Container();
			$container->set('foo', 'bar');
			$this->assertTrue($container->has('foo'));
		}

		public function testHasNot()
		{
			$container = new Data\Container();
			$this->assertFalse($container->has('foo'));
		}

		public function testImport()
		{
			$container = new Data\Container();
			$this->assertTrue($container->import(array('foo' => 'bar')));
		}

		public function testImportFailed()
		{
			$container = new Data\Container();
			$container->set('foo', 'bar');
			$this->assertFalse($container->import(array('foo' => 'baz')));
		}

		public function testImportOverride()
		{
			$container = new Data\Container();
			$container->set('foo', 'bar');
			$this->assertTrue($container->import(array('foo' => 'baz'), TRUE));
		}

		public function testExport()
		{
			$container = new Data\Container();
			$container->set('foo', 'bar');
			$this->assertEqual($container->export(), array('foo' => 'bar'));
		}

		public function testExportEmpty()
		{
			$container = new Data\Container();
			$this->assertEqual($container->export(), array());
		}

		public function testExportInitial()
		{
			$data = array('foo' => 'bar');
			$container = new Data\Container($data);
			$this->assertEqual($container->export(), $data);
		}
	}
}
// End of file: Container.test.php
// Location: test/library/data/Container.test.php