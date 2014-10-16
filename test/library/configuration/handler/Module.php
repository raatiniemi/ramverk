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
	 * Unit test case for the module configuration handler.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Module extends \UnitTestCase
	{
		public function testSimple()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<settings>
						<setting name="foo">bar</setting>
					</settings>
				</configuration>'
			);

			$config = new \MockConfig();
			$module = new Handler\Module($config);

			$this->assertEqual($module->execute($document), array('module.foo' => 'bar'));
		}

		public function testEmptyDocument()
		{
			$document = new Dom\Document();

			$config = new \MockConfig();
			$module = new Handler\Module($config);

			$this->assertEqual($module->execute($document), array());
		}

		public function testSettingWithoutName()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<settings>
						<setting foo="bar">baz</setting>
					</settings>
				</configuration>'
			);

			$config = new \MockConfig();
			$module = new Handler\Module($config);

			$this->expectException();
			$module->execute($document);
		}

		public function testMultipleSettings()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<settings>
						<setting name="foo">bar</setting>
					</settings>
					<settings>
						<setting name="baz">qux</setting>
					</settings>
				</configuration>'
			);

			$config = new \MockConfig();
			$module = new Handler\Module($config);

			$data = array('module.foo' => 'bar', 'module.baz' => 'qux');
			$this->assertEqual($module->execute($document), $data);
		}
	}
}
// End of file: Module.php
// Location: test/library/configuration/handler/Module.php