<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Functionality for dispatching actions.
	 *
	 * @package Ramverk
	 * @subpackage Controller
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Controller
	{
		protected $_context;

		public function __construct(Core\Context $context)
		{
			$this->_context = $context;
		}

		// TODO: Move to Routing class.
		protected $_route;

		public function dispatch()
		{
			$factory = $this->_context->getConfigurationHandlerFactory();
			$routes = $factory->callHandler('Routing', '%directory.application.config%/routing.xml');

			// TODO: Within the Routing class include these methods,
			// getModule/getAction with ucfirst and strtolower convertion.
			// method should be handled within the controller dispatch.
			$uri = isset($_GET['uri']) ? $_GET['uri'] : '';
			foreach($routes as $route) {
				if(preg_match("#{$route['pattern']}#", $uri)) {
					$this->_route = $route;
					break;
				}
			}

			// If no route have been found.
			if($this->_route === NULL) {
				throw new Exception('#404 - No route have been found.');
			}
		}
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php