<?php
namespace Me\Raatiniemi\Ramverk\Configuration
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Data\Dom;

	/**
	 * Base functionality for configuration handlers.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	abstract class Handler
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Utility;

		/**
		 * Configuration container.
		 * @var Me\Raatiniemi\Ramverk\Configuration\Container
		 */
		protected $_config;

		/**
		 * Initialize the configuration handler.
		 * @param Me\Raatiniemi\Ramverk\Configuration\Container $config Configuration container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct(Ramverk\Configuration $config)
		{
			$this->_config = $config;
		}

		/**
		 * Get the configuration container, used by Utility-trait.
		 * @return Me\Raatiniemi\Ramverk\Configuration\Container Configuration container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getConfig()
		{
			return $this->_config;
		}

		/**
		 * Execute the configuration handler.
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		abstract public function execute(Dom\Document $document);
	}
}
// End of file: Handler.class.php
// Location: library/configuration/handler/Handler.class.php