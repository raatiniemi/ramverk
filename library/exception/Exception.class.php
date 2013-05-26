<?php
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * @package Ramverk
	 * @subpackage Exception
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Exception extends \Exception
	{
		/**
		 * Render the exception message with template.
		 * @param Exception $e Thrown exception.
		 * @param Net\TheDeveloperBlog\Ramverk\Config $config Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public static function render(\Exception $e, Config $config)
		{
			echo $e->getMessage();
			exit($e->getCode());
		}
	}
}
// End of file: Exception.class.php
// Location: library/exception/Exception.class.php