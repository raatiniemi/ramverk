<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Configuration as Config;

	/**
	 * Unit test case for the configuration utility.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Utility extends \UnitTestCase
	{
		public function testExpandDirective()
		{
			$config = new Config();
			$config->set('foo', 'bar');

			$this->assertEqual($config->expandDirectives('%foo%'), 'bar');
		}

		public function testExpandDirectives()
		{
			$config = new Config();
			$config->set('foo', 'bar');
			$config->set('baz', 'qux');

			$this->assertEqual($config->expandDirectives('%foo%%baz%'), 'barqux');
		}

		public function testExpandDirectiveWithoutDirectives()
		{
			$config = new Config();

			$this->assertEqual($config->expandDirectives('%foo%'), '%foo%');
		}

		public function testExpandDirectiveWithoutReference()
		{
			$config = new Config();

			$this->assertEqual($config->expandDirectives('foo'), 'foo');
		}
	}
}
// End of file: Utility.test.php
// Location: test/library/configuration/Utility.test.php