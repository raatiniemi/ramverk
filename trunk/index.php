<?php
namespace Me\Raatiniemi\Ramverk\Trunk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Configuration;
	use Me\Raatiniemi\Ramverk\Request;
	use Me\Raatiniemi\Ramverk\Response;
	use Me\Raatiniemi\Ramverk\Routing;

	try {
		// Enable full error reporting.
		error_reporting(E_ALL);

		// Require the framework bootstrap file, the autoload functionality
		// within the framework will handle the inclusion of the other files.
		$directory = realpath(__DIR__ . '/..');
		require "{$directory}/src/ramverk.php";

		// Setup the basic application directory configurations.
		$config = new Configuration\Container();

		// Set the absolute path for the framework core and the application.
		$config->set('directory.core', "{$directory}/src", FALSE, TRUE);
		$config->set('directory.application', "{$directory}/trunk", FALSE, TRUE);

		// Initialize the framework core.
		$core = new Ramverk\Core($config);

		// ---- controller->dispatch() code.

		/**
		 * TODO: Figure out a way to loosen the coupling between controller and request, reponse, and routing.
		 * The controller needs to be testable, hence the mentioned objects needs to
		 * be injected to the controller and not instansiated within the controller.
		 *
		 * The action and view (and later model) will most likley have to be instansiated
		 * within the controller, since that is basically the purpose of the controller dispatch.
		 *
		 * TODO: Implement support for request and response data.
		 * The request data will be headers, post and get data, json data, etc. Based on the
		 * request data different arguments should be sent to the action. The action in turn
		 * will talk to the model and retrieve the response data.
		 *
		 * The reponse will need access to the request data aswell, since the accept headers
		 * will be located there and those values will determind which content type to send
		 * back to the user.
		 */

		/*
			Array
			(
				[directory.core] => /var/www/ramverk/src
				[directory.application] => /var/www/ramverk/trunk
				[profile] => development
				[context] => web
				[exception.template] => %directory.core.template%/exception/plaintext.php
				[directory.core.config] => %directory.core%/config
				[directory.core.library] => %directory.core%/library
				[directory.core.template] => %directory.core%/template
				[directory.application.cache] => %directory.application%/cache
				[directory.application.config] => %directory.application%/config
				[directory.application.library] => %directory.application%/library
				[directory.application.module] => %directory.application%/module
				[directory.application.template] => %directory.application%/template
			)
		*/

		// Retrieve the configuration container and the configuration handler factory.
		$config = $core->getContext()->getConfig();
		$factory = $core->getConfigurationHandlerFactory();

		print_r($config->export());

		// Setup the base namespace for the framework and the context name.
		// Since the context name will represent certain elements of the
		// structure it has to be formated accordingly, i.e. first letter in
		// uppercase followed by lowercase.
		$namespace['base'] = 'Me\\Raatiniemi\\Ramverk';
		$context = ucfirst(strtolower($config->get('context')));
	} catch(\Exception $e) {
		// Render thrown exceptions with the specified template.
		Ramverk\Exception::render($e, $config);
	}
}