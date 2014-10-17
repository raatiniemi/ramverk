<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Configuration\Handler;
	use Me\Raatiniemi\Ramverk\Data\Dom;

	\Mock::generate('\\Me\\Raatiniemi\\Ramverk\\Configuration', 'MockConfig');

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
		public function testSimpleRoute()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<routes>
						<route name="foo" pattern="^$" module="bar" action="baz" />
					</routes>
				</configuration>'
			);

			$config = new \MockConfig();
			$routing = new Handler\Routing($config);

			$this->assertEqual($routing->execute($document), array(
				array(
					'name' => 'foo',
					'pattern' => '^$',
					'module' => 'bar',
					'action' => 'baz'
				)
			));
		}

		public function testEmptyRoutes()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<routes>
					</routes>
				</configuration>'
			);

			$config = new \MockConfig();
			$routing = new Handler\Routing($config);

			$this->assertEqual($routing->execute($document), array());
		}

		public function testRouteWithoutName()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<routes>
						<route pattern="^$" module="foo" action="bar" />
					</routes>
				</configuration>'
			);

			$config = new \MockConfig();
			$routing = new Handler\Routing($config);

			$this->expectException();
			$routing->execute($document);
		}

		public function testRouteWithoutPattern()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<routes>
						<route name="foo" module="bar" action="baz" />
					</routes>
				</configuration>'
			);

			$config = new \MockConfig();
			$routing = new Handler\Routing($config);

			$this->expectException();
			$routing->execute($document);
		}

		public function testRouteWithoutModule()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<routes>
						<route name="foo" pattern="^$" action="bar" />
					</routes>
				</configuration>'
			);

			$config = new \MockConfig();
			$routing = new Handler\Routing($config);

			$this->expectException();
			$routing->execute($document);
		}

		public function testRouteWithoutAction()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<routes>
						<route name="foo" pattern="bar" module="baz" />
					</routes>
				</configuration>'
			);

			$config = new \MockConfig();
			$routing = new Handler\Routing($config);

			$this->expectException();
			$routing->execute($document);
		}

		public function testNestedRoute()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<routes>
						<route name="foo" pattern="^bar" module="baz">
							<route name=".qux" pattern="/{id:\d+}$" action="quux" />
						</route>
					</routes>
				</configuration>'
			);

			$config = new \MockConfig();
			$routing = new Handler\Routing($config);

			$route = array(
				'name' => 'foo.qux',
				'pattern' => '^bar/{id:\d+}$',
				'module' => 'baz',
				'action' => 'quux'
			);
			$this->assertEqual($routing->execute($document), array($route));
		}

		public function testNestedRoutes()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<routes>
						<route name="foo" pattern="^bar" module="baz">
							<route name=".qux" pattern="/{id:\d+}$" action="quux" />
							<route name=".corge" pattern="/{name:\w+}$" action="grault" />
						</route>
						<route name="waldo" module="fred">
							<route name=".xyzzy" pattern="^{id:\d+}$" action="thud" />
						</route>
					</routes>
				</configuration>'
			);

			$config = new \MockConfig();
			$routing = new Handler\Routing($config);

			$routes = array(
				array(
					'name' => 'foo.qux',
					'pattern' => '^bar/{id:\d+}$',
					'module' => 'baz',
					'action' => 'quux'
				),
				array(
					'name' => 'foo.corge',
					'pattern' => '^bar/{name:\w+}$',
					'module' => 'baz',
					'action' => 'grault'
				),
				array(
					'name' => 'waldo.xyzzy',
					'pattern' => '^{id:\d+}$',
					'module' => 'fred',
					'action' => 'thud'
				)
			);
			$this->assertEqual($routing->execute($document), $routes);
		}
	}
}
// End of file: Routing.php
// Location: test/library/configuration/handler/Routing.php