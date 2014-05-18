<?php
namespace Me\Raatiniemi\Ramverk\Core\Context
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Core;

	/**
	 * Easier handling for classes dependant on the application context.
	 *
	 * @package Ramverk
	 * @subpackage Core
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2014, Authors
	 */
	trait Handler
	{
		/**
		 * Stores the application context.
		 * @var Me\Raatiniemi\Ramverk\Core\Context
		 */
		private $_context;

		/**
		 * Set the application context.
		 * @param Me\Raatiniemi\Ramverk\Core\Context $context Application context.
		 * @return Me\Raatiniemi\Ramverk\Core\Context Application context.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function setContext(Core\Context $context)
		{
			return $this->_context = $context;
		}

		/**
		 * Retrieve the application context.
		 * @return Me\Raatiniemi\Ramverk\Core\Context Application context.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getContext()
		{
			return $this->_context;
		}

		/**
		 * Retrieve configuration container.
		 * @return Me\Raatiniemi\Ramverk\Configuration\Container Configuration Container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getConfig()
		{
			return $this->_context->getConfig();
		}
	}
}
// End of file: Handler.trait.php
// Location: library/core/context/Handler.trait.php