<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	// Require the absolute core classes needed to bootstrap the framework.
	// With these included the autoload functionality can handle the rest.
	require __DIR__ . '/library/loader/Autoload.trait.php';
	require __DIR__ . '/library/configuration/Utility.trait.php';
	require __DIR__ . '/library/core/Core.class.php';
	require __DIR__ . '/library/core/Context.class.php';

	// Include the configuration container with its core dependencies.
	require __DIR__ . '/library/data/Container.class.php';
	require __DIR__ . '/library/configuration/Container.class.php';

	// Include the configuration handlers required by the core.
	require __DIR__ . '/library/configuration/handler/Cache.class.php';
	require __DIR__ . '/library/configuration/handler/Parser.class.php';
	require __DIR__ . '/library/configuration/handler/Factory.class.php';
	require __DIR__ . '/library/configuration/handler/Handler.class.php';
	require __DIR__ . '/library/configuration/handler/Autoload.class.php';

	// Include the extended DOM functionality.
	require __DIR__ . '/library/data/dom/utility/Value.trait.php';
	require __DIR__ . '/library/data/dom/Document.class.php';
	require __DIR__ . '/library/data/dom/Element.class.php';
	require __DIR__ . '/library/data/dom/Node.class.php';

	// Include exceptions.
	require __DIR__ . '/library/exception/Exception.class.php';
}
// End of file: ramverk.php
// Location: ramverk.php