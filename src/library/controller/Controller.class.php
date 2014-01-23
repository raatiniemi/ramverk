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
		protected $_request;

		protected $_response;

		public function __construct(Request $request, Response $response)
		{
			$this->_request = $request;
			$this->_response = $response;
		}

		public function dispatch()
		{
		}
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php