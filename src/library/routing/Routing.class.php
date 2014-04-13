<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	abstract class Routing
	{
		protected $_request;

		protected $_routes;

		protected $_module;

		protected $_action;

		protected $_params;

		public function __construct(Request $request, array $routes=array())
		{
			$this->_request = $request;
			$this->_routes = $routes;
			$this->_params = array();
		}

		public function getModule()
		{
			return ucfirst(strtolower($this->_module));
		}

		public function getAction()
		{
			return ucfirst(strtolower($this->_action));
		}

		abstract public function getActionMethod(\ReflectionClass $reflection);

		public function getParams()
		{
			return $this->_params;
		}

		abstract public function parse();
	}
}
// End of file: Routing.class.php
// Location: library/routing/Routing.class.php