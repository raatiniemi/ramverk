<?php
namespace Me\Raatiniemi\Ramverk\Test
{
	// Setup the ramverk include path.
	$path = realpath(__DIR__ . '/..');
	set_include_path($path . PATH_SEPARATOR . get_include_path());

	// Include the library classes required for the unit test cases.
	require_once 'src/library/configuration/Utility.trait.php';
	require_once 'src/library/configuration/Configuration.class.php';
	require_once 'src/library/configuration/handler/Handler.class.php';
	require_once 'src/library/configuration/handler/Autoload.class.php';
	require_once 'src/library/configuration/handler/Core.class.php';
	require_once 'src/library/configuration/handler/Module.class.php';
	require_once 'src/library/configuration/handler/Routing.class.php';
	require_once 'src/library/configuration/handler/Cache.class.php';

	require_once 'src/library/data/dom/utility/Value.trait.php';
	require_once 'src/library/data/dom/Attribute.class.php';
	require_once 'src/library/data/dom/Document.class.php';
	require_once 'src/library/data/dom/Element.class.php';
	require_once 'src/library/data/dom/Node.class.php';

	require_once 'src/library/exception/Exception.class.php';
}
// End of file: ramverk.php
// Location: test/ramverk.php