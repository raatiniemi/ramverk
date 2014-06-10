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

		/**
		 * Retrieve the configuration handler factory, used by the autoload-trait.
		 * @return Me\Raatiniemi\Ramverk\Configuration\Handler\Factory Configuration handler factory.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getConfigurationHandlerFactory()
		{
			return $this->getContext()->getConfigurationHandlerFactory();
		}
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php