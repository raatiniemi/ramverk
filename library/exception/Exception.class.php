<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk\Configuration;

	/**
	 * Base for framework exceptions.
	 *
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
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Container $config Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 *
		 * @todo How to implement configuration utilities when method is static.
		 */
		public static function render(\Exception $e, Configuration\Container $config)
		{
			echo $e->getMessage();
			exit($e->getCode());
		}
	}
}
// End of file: Exception.class.php
// Location: library/exception/Exception.class.php