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
		 * Execute the configuration handler
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document)
		{
			$data = array();
			foreach($document->getConfigurationElements() as $configuration) {
				foreach($configuration->get('routes') as $routes) {
					foreach($routes->get('route') as $route) {
						$item = array();

						// Verify that the route have all of the required attributes.
						foreach(array('name', 'pattern', 'module', 'action') as $attribute) {
							if(!$route->hasAttribute($attribute)) {
								// TODO: Throw exception, required attribute is missing.
							}
							// Assign the attribute value to the route configuration.
							$item[$attribute] = $route->getAttribute($attribute);
						}

						// Add the route configuration to the routes.
						$data[] = $item;
					}
				}
			}
			return $data;
		}
	}
}
// End of file: Routing.class.php
// Location: library/configuration/handler/Routing.class.php