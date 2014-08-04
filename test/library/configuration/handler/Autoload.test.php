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
	 * Unit test case for the autoload configuration handler.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Autoload extends \UnitTestCase
	{
		public function testSimple()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<autoloads>
						<autoload name="Foo">Bar.php</autoload>
					</autoloads>
				</configuration>'
			);

			$config = new \MockConfig();
			$autoload = new Handler\Autoload($config);

			$this->assertEqual($autoload->execute($document), array('Foo' => 'Bar.php'));
		}

		public function testWithMultipleItems()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<autoloads>
						<autoload name="Foo">Bar.php</autoload>
						<autoload name="Baz">Qux.php</autoload>
					</autoloads>
				</configuration>'
			);

			$config = new \MockConfig();
			$autoload = new Handler\Autoload($config);

			$data = array('Foo' => 'Bar.php', 'Baz' => 'Qux.php');
			$this->assertEqual($autoload->execute($document), $data);
		}

		public function testWithGroupNamespace()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<autoloads namespace="Foo">
						<autoload name="Bar">Baz.php</autoload>
					</autoloads>
				</configuration>'
			);

			$config = new \MockConfig();
			$autoload = new Handler\Autoload($config);

			$data = array('Foo\\Bar' => 'Baz.php');
			$this->assertEqual($autoload->execute($document), $data);
		}

		public function testWithMultipleGroupNamespaces()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<autoloads namespace="Foo">
						<autoload name="Bar">Baz.php</autoload>
					</autoloads>
					<autoloads namespace="Qux">
						<autoload name="Quux">Corge.php</autoload>
					</autoloads>
				</configuration>'
			);

			$config = new \MockConfig();
			$autoload = new Handler\Autoload($config);

			$data = array('Foo\\Bar' => 'Baz.php', 'Qux\\Quux' => 'Corge.php');
			$this->assertEqual($autoload->execute($document), $data);
		}

		public function testWithItemNamespace()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<autoloads>
						<autoload name="Foo\\Bar">Baz.php</autoload>
					</autoloads>
				</configuration>'
			);

			$config = new \MockConfig();
			$autoload = new Handler\Autoload($config);

			$data = array('Foo\\Bar' => 'Baz.php');
			$this->assertEqual($autoload->execute($document), $data);
		}

		public function testWithGroupAndItemNamespace()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<autoloads namespace="Foo">
						<autoload name="Bar\\Baz">Qux.php</autoload>
					</autoloads>
				</configuration>'
			);

			$config = new \MockConfig();
			$autoload = new Handler\Autoload($config);

			$data = array('Foo\\Bar\\Baz' => 'Qux.php');
			$this->assertEqual($autoload->execute($document), $data);
		}

		public function testWithMissingClassName()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<autoloads>
						<autoload>Foo.php</autoload>
					</autoloads>
				</configuration>'
			);

			$config = new \MockConfig();
			$autoload = new Handler\Autoload($config);

			$this->expectException();
			$autoload->execute($document);
		}

		public function testWithEmptyDocument()
		{
			$document = new Dom\Document();
			$document->loadXML('<configuration></configuration>');

			$config = new \MockConfig();
			$autoload = new Handler\Autoload($config);

			$this->assertEqual($autoload->execute($document), array());
		}
	}
}
// End of file: Autoload.test.php
// Location: test/library/configuration/handler/Autoload.test.php