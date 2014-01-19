<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Configuration;
	use Me\Raatiniemi\Ramverk\Data\Dom;

	/**
	 * Handler for routing configuration.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Routing extends Configuration\Handler
	{
		/**
		 * Execute the configuration handler.
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document)
		{
			$routes = array();
			foreach($document->getElementsByTagName('route') as $route) {
				$routes[] = $this->parseRoute($route);
			}
			return $routes;
		}

		/**
		 * Parse the route configuration.
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Element $route Element with route configuration.
		 * @return array Parsed route.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		private function parseRoute(Dom\Element $route)
		{
			$config = array();

			// Loop through each of the required attributes.
			$required = array('name', 'pattern', 'module', 'action');
			foreach($required as $attribute) {
				if(!$route->hasAttribute($attribute)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Route is missing required "%s"-attribute.',
						$attribute
					));
				}
				$config[$attribute] = $route->getAttribute($attribute);
			}

			if($route->hasAttribute('method')) {
				$config['method'] = $route->getAttribute('method');
			}

			if($route->hasAttribute('outputType')) {
				$config['outputType'] = $route->getAttribute('outputType');
			}

			return $config;
		}
	}
}
// End of file: Routing.class.php
// Location: library/configuration/handler/Routing.class.php