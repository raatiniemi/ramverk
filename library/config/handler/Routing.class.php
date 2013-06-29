<?php
namespace Net\TheDeveloperBlog\Ramverk\Config\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * @package Ramverk
	 * @subpackage Config
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Routing implements IHandler
	{
		/**
		 * Execute the configuration handler.
		 * @param DOMDocument $document XML document with configuration data.
		 * @param Net\TheDeveloperBlog\Ramverk\Config $config Configuration container.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function execute(\DOMDocument $document, Ramverk\Config $config)
		{
			$routes = array();
			foreach($document->getElementsByTagName('route') as $route) {
				$routes[] = $this->parseRoute($route);
			}
			return $routes;
		}

		/**
		 * Parse the route configuration.
		 * @param DOMElement $route Element with route configuration.
		 * @return array Parsed route.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		private function parseRoute(\DOMElement $route)
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
// Location: library/config/handler/Routing.class.php