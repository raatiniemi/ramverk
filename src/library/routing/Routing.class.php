<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Generic functionality for handling request routing.
	 *
	 * @package Ramverk
	 * @subpackage Routing
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2014, Authors
	 *
	 * @abstract
	 */
	abstract class Routing
	{
		/**
		 * The request.
		 * @var Me\Raatiniemi\Ramverk\Request
		 */
		private $_rq;

		/**
		 * The available request routes.
		 * @var array
		 */
		private $_routes;

		/**
		 * Name of the module retrieved from parsing the request routing.
		 * @var string
		 */
		private $_module;

		/**
		 * Name of the action retrieved from parsing the request routing.
		 * @var string
		 */
		private $_action;

		/**
		 * Parameters retrieved from parsing the request routing.
		 * @var array
		 */
		private $_params;

		/**
		 * Initialize the routing.
		 * @param Me\Raatiniemi\Ramverk\Request $rq The request.
		 * @param array $routes The available routing routes.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct(Request $rq, array $routes=array())
		{
			$this->setRequest($rq);
			$this->setRoutes($routes);
			$this->setParams(array());
		}

		/**
		 * Set the request.
		 * @param Me\Raatiniemi\Ramvekr\Request $rq The request.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		private function setRequest(Request $rq)
		{
			$this->_rq = $rq;
		}

		/**
		 * Retrieve the request.
		 * @return Me\Raatiniemi\Ramverk\Request The request.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function getRequest()
		{
			return $this->_rq;
		}

		/**
		 * Retrieve the action method.
		 *
		 * Depending on the request method (e.g. read, write, etc.) the action
		 * method might change, if the action have defined the method.
		 *
		 * If the request method is POST and the action have defined the method
		 * `executeWrite` that name will be returned. However, if the method is
		 * not defined, the generic `execute` name will be returned.
		 * @param ReflectionClass $reflection Reflection of the action.
		 * @return string Action method.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getActionMethod(\ReflectionClass $reflection)
		{
			// Default method if none have been found.
			$method = 'execute';

			// Assemble the list of available action methods.
			$availableMethods = array();
			if($this->getRequest()->getMethod() === Request::Write) {
				$availableMethods[] = Request::Write;
			}
			// The `executeRead` method should always be available as fallback,
			// if none of the other action methods have been implemented.
			$availableMethods[] = Request::Read;

			// Iterate through the available action methods and try to find
			// the first available.
			foreach($availableMethods as $actionMethod) {
				if($reflection->hasMethod("execute{$actionMethod}")) {
					$method = "execute{$actionMethod}";
					break;
				}
			}

			return $method;
		}

		/**
		 * Check whether a route have been found.
		 *
		 * The criteria on whether a route have been found is that both the
		 * module and action have been set.
		 * @return boolean True if route have been found, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function hasRoute()
		{
			return (bool)isset($this->_module, $this->_action);
		}

		/**
		 * Set the available routes
		 * @param array $routes Available routes.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		private function setRoutes(array $routes=array())
		{
			$this->_routes = $routes;
		}

		/**
		 * Retrieve the available routes.
		 * @return array Available routes.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function getRoutes()
		{
			return $this->_routes;
		}

		/**
		 * Set the module name.
		 * @param string $module Name of the module.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function setModule($module)
		{
			$this->_module = $module;
		}

		/**
		 * Retrieve the module name.
		 * @return string Name of the module.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getModule()
		{
			return ucfirst(strtolower($this->_module));
		}

		/**
		 * Set the action name.
		 * @param string $action Name of the action.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function setAction($action)
		{
			$this->_action = $action;
		}

		/**
		 * Retrieve the module name.
		 * @return string Name of the action.
		 * @author Tobias Raatinimie <raatiniemi@gmail.com>
		 */
		public function getAction()
		{
			return $this->_action;
		}

		/**
		 * Set the request parameters.
		 * @param array $params Request parameters.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function setParams(array $params=array())
		{
			$this->_params = $params;
		}

		/**
		 * Retrieve the request parameters.
		 * @return array Request parameters.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getParams()
		{
			return $this->_params;
		}

		/**
		 * Parse the available routes after a match against the request.
		 * @return boolean True if a route have been found, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		abstract public function parse();
	}
}
// End of file: Routing.class.php
// Location: library/routing/Routing.class.php