<?php
namespace Me\Raatiniemi\Ramverk\Test
{
	$ramverk = realpath(__DIR__ . '/..');
	$library = "{$ramverk}/library";

	require_once "{$ramverk}/vendor/simpletest/autorun.php";

	require_once "{$library}/data/Container.class.php";
	require_once "{$library}/configuration/Utility.trait.php";
	require_once "{$library}/configuration/Container.class.php";
	require_once "{$library}/configuration/handler/Handler.class.php";
	require_once "{$library}/configuration/handler/Autoload.class.php";

	require_once "{$library}/data/dom/utility/Value.trait.php";
	require_once "{$library}/data/dom/Document.class.php";
	require_once "{$library}/data/dom/Element.class.php";
	require_once "{$library}/data/dom/Node.class.php";

	require_once "{$library}/exception/Exception.class.php";

	class Suite extends \TestSuite
	{
		public function __construct()
		{
			parent::__construct('Me\\Raatiniemi\\Ramverk\\Test\\Suite');

			$this->addFile(__DIR__ . '/library/configuration/handler/Autoload.test.php');
		}
	}
}