<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler {
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
	class Routing extends Configuration\Handler {
		/**
		 * Execute the configuration handler
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document) {
			$data = array();
			foreach($document->getConfigurationElements() as $configuration) {
				foreach($configuration->get('routes') as $routes) {
					if($routes->has('route')) {
						// Retrieve the routes from the document section.
						$data = array_merge($data, $this->getRoutes($routes->get('route')));
					}
				}
			}
			return $data;
		}

		/**
		 * Retrieve route components from the configuration.
		 * @param DOMNodeList $nodes List of route components to extract data.
		 * @param array $parent Previous route component.
		 * @return array Route components.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		private function getRoutes(\DOMNodeList $nodes, array $parent = array()) {
			$routes = array();
			foreach($nodes as $node) {
				$route = $parent;

				// Check if the route have defined subroutes.
				if($node->has('route')) {
					// Iterate through the name and pattern.
					foreach(array('name', 'pattern') as $attribute) {
						if($node->hasAttribute($attribute)) {
							// The name and pattern of the parent route component (if
							// any) should prefix the current route component.
							$prefix = isset($route[$attribute]) ? $route[$attribute] : null;
							$route[$attribute] = "{$prefix}{$node->getAttribute($attribute)}";
						}
					}

					// Iterate through the module and action. The module and
					// action values of the current route component should
					// replace the parent route components values, if defined.
					foreach(array('module', 'action') as $attribute) {
						if($node->hasAttribute($attribute)) {
							$route[$attribute] = $node->getAttribute($attribute);
						}
					}

					// Retrieve the subroutes and merge with the available routes.
					$routes = array_merge($routes, $this->getRoutes($node->get('route'), $route));
				} else {
					// Add the route to the available routes.
					$routes[] = $this->getRoute($node, $parent);
				}
			}
			return $routes;
		}

		/**
		 * Retrieve the route component.
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Element $node Dom element for the route component.
		 * @param array $route Parent route component, if any.
		 * @throws Me\Raatiniemi\Ramverk\Exception If route component is missing name or pattern.
		 * @throws Me\Raatiniemi\Ramverk\Exception If route component is missing module or action.
		 * @return array Route component.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		private function getRoute(Dom\Element $node, array $route = array()) {
			// Iterate through the name and pattern.
			foreach(array('name', 'pattern') as $attribute) {
				if($node->hasAttribute($attribute)) {
					// The name and pattern of the parent route component (if
					// any) should prefix the current route component.
					$prefix = isset($route[$attribute]) ? $route[$attribute] : null;
					$route[$attribute] = "{$prefix}{$node->getAttribute($attribute)}";
				}

				// Both the name and pattern have to be defined on every route.
				// It can be inherited from a parent route component, but every
				// route must have them.
				if(!isset($route[$attribute])) {
					// TODO: Write exception message.
					throw new Ramverk\Exception;
				}
			}

			// Iterate through the module and action. The module and action
			// values of the current route component should replace the parent
			// route components values, if defined.
			foreach(array('module', 'action') as $attribute) {
				if($node->hasAttribute($attribute)) {
					$route[$attribute] = $node->getAttribute($attribute);
				}

				// Both the module and action have to be defined on every route.
				// It can be inherited from a parent route component, but every
				// route must have them.
				if(!isset($route[$attribute])) {
					// TODO: Write exception message.
					throw new Ramverk\Exception;
				}
			}
			return $route;
		}
	}
}
// End of file: Routing.class.php
// Location: library/configuration/handler/Routing.class.php