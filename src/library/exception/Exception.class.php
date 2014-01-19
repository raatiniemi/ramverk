<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Configuration;

	/**
	 * Base for framework exceptions.
	 *
	 * @package Ramverk
	 * @subpackage Exception
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Exception extends \Exception
	{
		/**
		 * Render the exception message with template.
		 * @param Exception $e Thrown exception.
		 * @param Me\Raatiniemi\Ramverk\Configuration\Container $config Configuration container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public static function render(\Exception $e, Configuration\Container $config)
		{
			// Require the exception template.
			require $config->expandDirectives('%exception.template%');

			// Exit the application with the specified code.
			exit($e->getCode());
		}
	}
}
// End of file: Exception.class.php
// Location: library/exception/Exception.class.php