<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Controller
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;
	use Net\TheDeveloperBlog\Ramverk\Configuration;

	/**
	 * Functionality for the web based controller.
	 *
	 * @package Ramverk
	 * @subpackage Controller
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Web extends Ramverk\Controller
	{
		/**
		 * Dispatch the controller.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 * @todo Handle disabled modules.
		 */
		public function dispatch()
		{
			$moduleName = ucfirst($this->_request->getModule());
			$actionName = ucfirst($this->_request->getAction());

			$directory = $this->expandDirectives("%directory.application.module%/{$moduleName}");
			if(!is_dir($directory)) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception("Module \"{$moduleName}\" do not exists.");
			}

			$autoload = "{$directory}/config/autoload.xml";
			if(file_exists($autoload)) {
				$this->_autoloadFile = $autoload;
				spl_autoload_register(array($this, 'autoload'), TRUE, TRUE);
			}

			$config = new Configuration\Container();

			$module = "{$directory}/config/module.xml";
			if(file_exists($module)) {
				$items = $this->getConfigurationHandlerFactory()->callHandler('Module', $module);
				$config->import($items);
			}

			// TODO: Handle namespace for actions, models and views.
			// If no namespace have been supplied for the specific module, the
			// application namespace should be used. If no namespace have been
			// supplied for the application, global namespace should be used.

			// TODO: Handle content types.

			$action['reflection'] = new \ReflectionClass($actionName);
			$action['method'] = $this->_request->getMethod() === 'POST' ? 'Write' : 'Read';
		}
	}
}
// End of file: Web.class.php
// Location: library/controller/Web.class.php