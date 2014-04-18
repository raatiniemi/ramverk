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
		public function testValidAutoloadDocument()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<autoloads>'.
					'<autoload name="Foo">Foo.php</autoload>'.
				'</autoloads>'
			);

			$container = new \MockContainer();
			$autoload = new Handler\Autoload($container);

			$this->assertEqual($autoload->execute($document), array('Foo' => 'Foo.php'));
		}

		public function testInvalidAutoloadDocument()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<autoloads>'.
					'<autoload class="Foo">Foo.php</autoload>'.
				'</autoloads>'
			);

			$container = new \MockContainer();
			$autoload = new Handler\Autoload($container);

			$this->expectException();
			$autoload->execute($document);
		}

		public function testEmptyAutoloadDocument()
		{
			$document = new Dom\Document();
			$document->loadXML('<configuration></configuration>');

			$container = new \MockContainer();
			$autoload = new Handler\Autoload($container);

			$this->expectException();
			$autoload->execute($document);
		}

		public function testAutoloadDocumentWithNamespaceOnItem()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<autoloads>'.
					'<autoload name="Foo\\Bar">foo/Bar.php</autoload>'.
				'</autoloads>'
			);

			$container = new \MockContainer();
			$autoload = new Handler\Autoload($container);

			$this->assertEqual($autoload->execute($document), array('Foo\\Bar' => 'foo/Bar.php'));
		}

		public function testAutoloadDocumentWithNamespaceOnGroup()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<autoloads namespace="Foo">'.
					'<autoload name="Bar">foo/Bar.php</autoload>'.
				'</autoloads>'
			);

			$container = new \MockContainer();
			$autoload = new Handler\Autoload($container);

			$this->assertEqual($autoload->execute($document), array('Foo\\Bar' => 'foo/Bar.php'));
		}
	}
}
// End of file: Autoload.test.php
// Location: test/library/configuration/handler/Autoload.test.php