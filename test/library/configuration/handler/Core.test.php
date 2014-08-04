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
	 * Unit test case for the core configuration handler.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Core extends \UnitTestCase
	{
		public function testWithDefaultAnd404SystemActions()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
						<system_action name="default">
							<module>Index</module>
							<action>Index</action>
						</system_action>
						<system_action name="404">
							<module>Error</module>
							<action>NotFound</action>
						</system_action>
					</system_actions>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$data = array(
				'actions.default_module' => 'Index',
				'actions.default_action' => 'Index',
				'actions.404_module' => 'Error',
				'actions.404_action' => 'NotFound'
			);
			$this->assertEqual($core->execute($document), $data);
		}

		public function testWithoutSystemActions()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
					</system_actions>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$this->expectException();
			$core->execute($document);
		}

		public function testWithoutDefaultSystemAction()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
						<system_action name="foo">
							<module>Index</module>
							<action>Index</action>
						</system_action>
					</system_actions>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$this->expectException();
			$core->execute($document);
		}

		public function testWithout404SystemAction()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
						<system_action name="default">
							<module>Index</module>
							<action>Index</action>
						</system_action>
					</system_actions>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$this->expectException();
			$core->execute($document);
		}

		public function testSystemActionWithoutName()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
						<system_action>
							<module>Index</module>
							<action>Index</action>
						</system_action>
					</system_actions>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$this->expectException();
			$core->execute($document);
		}

		public function testSystemActionWithoutModuleElement()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
						<system_action name="default">
							<action>Index</action>
						</system_action>
					</system_actions>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$this->expectException();
			$core->execute($document);
		}

		public function testSystemActionWithoutModuleValue()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
						<system_action name="default">
							<module></module>
							<action>Index</action>
						</system_action>
					</system_actions>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$this->expectException();
			$core->execute($document);
		}

		public function testSystemActionWithoutActionElement()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
						<system_action name="default">
							<module>Index</module>
						</system_action>
					</system_actions>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$this->expectException();
			$core->execute($document);
		}

		public function testSystemActionWithoutActionValue()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
						<system_action name="default">
							<module>Index</module>
							<action></action>
						</system_action>
					</system_actions>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$this->expectException();
			$core->execute($document);
		}

		public function testSimpleSettings()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<system_actions>
						<system_action name="default">
							<module>Index</module>
							<action>Index</action>
						</system_action>
						<system_action name="404">
							<module>Error</module>
							<action>NotFound</action>
						</system_action>
					</system_actions>
					<settings>
						<setting name="foo">bar</setting>
					</settings>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$data = array(
				'actions.default_module' => 'Index',
				'actions.default_action' => 'Index',
				'actions.404_module' => 'Error',
				'actions.404_action' => 'NotFound',
				'core.foo' => 'bar'
			);
			$this->assertEqual($core->execute($document), $data);
		}

		public function testSettingWithoutName()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<configuration>
					<settings>
						<setting />
					</settings>
				</configuration>'
			);

			$config = new \MockConfig();
			$core = new Handler\Core($config);

			$this->expectException();
			$core->execute($document);
		}
	}
}
// End of file: Core.test.php
// Location: test/library/configuration/handler/Core.test.php