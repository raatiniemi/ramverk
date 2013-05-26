<?php
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	// Require the absolute core classes needed to bootstrap the framework.
	// With these included the autoload functionality can handle the rest.
	require __DIR__ . '/library/core/Core.class.php';
	require __DIR__ . '/library/config/Config.class.php';
	require __DIR__ . '/library/config/handler/Cache.class.php';
	require __DIR__ . '/library/config/handler/Parser.class.php';
	require __DIR__ . '/library/config/handler/Factory.class.php';
	require __DIR__ . '/library/config/handler/IHandler.interface.php';
	require __DIR__ . '/library/config/handler/Autoload.class.php';
	require __DIR__ . '/library/exception/Exception.class.php';
}
// End of file: ramverk.php
// Location: ramverk.php