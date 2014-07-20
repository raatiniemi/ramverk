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
		public function testValidSystemActionSection()
		{
			$document = new Dom\Document();
			$document->loadXML(
				'<system_actions>'.
					'<system_action name="default">'.
						'<module>Index</module>'.
						'<action>Index</action>'.
					'</system_action>'.
				'</system_actions>'
			);

			$container = new \MockContainer();
			$core = new Handler\Core($container);

			$data = array(
				'actions.default_module' => 'Index',
				'actions.default_action' => 'Index'
			);
		}
	}
}
// End of file: Core.test.php
// Location: test/library/configuration/handler/Core.test.php