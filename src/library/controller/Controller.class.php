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
	final class Controller
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Core\Context\Handler;
		use Loader\Autoload;

		/**
		 * Initialize the controller.
		 * @param Me\Raatiniemi\Ramverk\Core\Context $ct Application context.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct(Core\Context $ct)
		{
			$this->setContext($ct);
		}

		public function dispatch()
		{
		}
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php