<?php
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	// Require the absolute core classes needed to bootstrap the framework.
	// With these included the autoload functionality can handle the rest.
	require __DIR__ . '/library/core/Core.class.php';

	// Include the configuration container with its core dependencies.
	require __DIR__ . '/library/data/Container.class.php';
	require __DIR__ . '/library/configuration/IUtility.interface.php';
	require __DIR__ . '/library/configuration/Utility.trait.php';
	require __DIR__ . '/library/configuration/Container.class.php';

	// Include the configuration handlers required by the core.
	require __DIR__ . '/library/configuration/handler/Cache.class.php';
	require __DIR__ . '/library/configuration/handler/Parser.class.php';
	require __DIR__ . '/library/configuration/handler/Factory.class.php';
	require __DIR__ . '/library/configuration/handler/IHandler.interface.php';
	require __DIR__ . '/library/configuration/handler/Autoload.class.php';

	require __DIR__ . '/library/exception/Exception.class.php';
}
// End of file: ramverk.php
// Location: ramverk.php