<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Configuration;
	use Me\Raatiniemi\Ramverk\Configuration\Handler;
	use Me\Raatiniemi\Ramverk\Data\Dom;

	\Mock::generate('\\Me\\Raatiniemi\\Ramverk\\Configuration\\Container', 'MockContainer');

	/**
	 * Unit test case for the routing configuration handler.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Routing extends \UnitTestCase
	{
		public function testEmptyRoutingDocument()
		{
			$document = new Dom\Document();
			$document->loadXML('<routes></routes>');

			$container = new \MockContainer();
			$routing = new Handler\Routing($container);

			$this->assertEqual($routing->execute($document), array());
		}

		public function testValidRoute()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<routes>'.
					'<route name="index" pattern="^$" module="index" action="index" />'.
				'</routes>'
			);

			$container = new \MockContainer();
			$routing = new Handler\Routing($container);

			$this->assertEqual($routing->execute($document), array(
				array(
					'name' => 'index',
					'pattern' => '^$',
					'module' => 'index',
					'action' => 'index'
				)
			));
		}

		public function testRouteMissingName()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<routes>'.
					'<route pattern="^$" module="index" action="index" />'.
				'</routes>'
			);

			$container = new \MockContainer();
			$routing = new Handler\Routing($container);

			$this->expectException();
			$routing->execute($document);
		}

		public function testRouteMissingPattern()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<routes>'.
					'<route name="index" module="index" action="index" />'.
				'</routes>'
			);

			$container = new \MockContainer();
			$routing = new Handler\Routing($container);

			$this->expectException();
			$routing->execute($document);
		}

		public function testRouteMissingModule()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<routes>'.
					'<route name="index" pattern="^$" action="index" />'.
				'</routes>'
			);

			$container = new \MockContainer();
			$routing = new Handler\Routing($container);

			$this->expectException();
			$routing->execute($document);
		}

		public function testRouteMissingAction()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<routes>'.
					'<route name="index" pattern="^$" module="index" />'.
				'</routes>'
			);

			$container = new \MockContainer();
			$routing = new Handler\Routing($container);

			$this->expectException();
			$routing->execute($document);
		}
	}
}
// End of file: Routing.test.php
// Location: test/library/configuration/handler/Routing.test.php