<?php
namespace Me\Raatiniemi\Ramverk\Test
{
	// Setup the ramverk include path.
	$path = realpath(__DIR__ . '/..');
	set_include_path($path . PATH_SEPARATOR . get_include_path());

	// Include the simpletest autorun.
	require_once "vendor/simpletest/autorun.php";

	// Include the library classes required for the unit test cases.
	require_once "src/library/data/Container.class.php";
	require_once "src/library/configuration/Utility.trait.php";
	require_once "src/library/configuration/Container.class.php";
	require_once "src/library/configuration/handler/Handler.class.php";
	require_once "src/library/configuration/handler/Autoload.class.php";

	require_once "src/library/data/dom/utility/Value.trait.php";
	require_once "src/library/data/dom/Document.class.php";
	require_once "src/library/data/dom/Element.class.php";
	require_once "src/library/data/dom/Node.class.php";

	require_once "src/library/exception/Exception.class.php";

	/**
	 * Test suite for the entire framework.
	 *
	 * @package Ramverk
	 * @subpackage Test
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Suite extends \TestSuite
	{
		/**
		 * List of file paths for the test cases, relative from project root.
		 * @var array
		 */
		private $_testCases = array(
			'test/library/configuration/handler/Autoload.test.php',
			'test/library/data/Container.test.php'
		);

		public function __construct()
		{
			parent::__construct(__CLASS__);

			// Iterate through each of the test case files and add them.
			foreach($this->_testCases as $testCase) {
				$this->addFile($testCase);
			}
		}
	}
}
// End of file: ramverk.php
// Location: test/ramverk.php