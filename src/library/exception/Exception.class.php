<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

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
		public static function render(\Exception $e, Configuration $config)
		{
			// There are a few different templates we can use.
			// First, the default exception template, defined from within the core.
			// Second, exception template defined from within the application code.
			// Third, exception template defined from within the application core configuration.
			//
			// If none of the application specified templates have been defined
			// the default template will be used as fallback.
			$template = $config->get('exception.template.default');
			$template = $config->get('exception.template', $template);
			$template = $config->get('core.exception.template', $template);
			$template = $config->expandDirectives($template);

			// Check that the specified template is readable.
			if(!is_readable($template)) {
				$template = $config->get('exception.template.default');
				$template = $config->expandDirectives($template);
			}

			// Since it's possible to override the default template we have to
			// do another check to see that it's readable.
			if(is_readable($template)) {
				// Everything seems fine, include the template.
				require $template;
			} else {
				// None of the specified templates are readable, something is wrong.
				echo 'The exception template can\'t be found, please check the permissions for the template<br>'. PHP_EOL;
				echo 'directories and verify that the template files exists.<br><br>'. PHP_EOL;

				// Retrieve the exception message, if available, and print it.
				$message = $e->getMessage();
				if(!empty($message)) {
					echo 'Message from thrown exception:<br>'. PHP_EOL;
					echo $e->getMessage();
				}
			}

			// Exit the application with the specified code.
			exit($e->getCode());
		}
	}
}
// End of file: Exception.class.php
// Location: library/exception/Exception.class.php