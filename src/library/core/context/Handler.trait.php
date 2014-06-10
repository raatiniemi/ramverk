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
		private $_ct;

		/**
		 * Set the application context.
		 * @param Me\Raatiniemi\Ramverk\Core\Context $ct Application context.
		 * @return Me\Raatiniemi\Ramverk\Core\Context Application context.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function setContext(Core\Context $ct)
		{
			return $this->_ct = $ct;
		}

		/**
		 * Retrieve the application context.
		 * @return Me\Raatiniemi\Ramverk\Core\Context Application context.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getContext()
		{
			return $this->_ct;
		}

		/**
		 * Retrieve configuration container.
		 * @return Me\Raatiniemi\Ramverk\Configuration\Container Configuration Container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getConfig()
		{
			return $this->getContext()->getConfig();
		}

		/**
		 * Retrieve the configuration handler factory.
		 * @return Me\Raatiniemi\Ramverk\Configuration\Handler\Factory Configuration handler factory.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getConfigurationHandlerFactory()
		{
			return $this->getContext()->getConfigurationHandlerFactory();
		}
	}
}
// End of file: Handler.trait.php
// Location: library/core/context/Handler.trait.php