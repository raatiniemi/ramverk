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
			$config = $this->initializeModule();

			$config->set('action.name', ucfirst(strtolower($this->getRequest()->getAction())));

			// TODO: Implement support for namespace fallback.
			// If no namespace have been supplied for the specific module, the
			// application namespace should be used. If no namespace have been
			// supplied for the application, global namespace should be used.

			$config->set('action.class', $config->get('action.name'));
			if($config->has('module.namespace')) {
				$config->set('action.class', $config->expandDirectives('%module.namespace%\\%module.name%\\Action\\%action.name%'), TRUE);
			}

			$action['reflection'] = new \ReflectionClass($config->get('action.class'));
			$action['action'] = $this->getRequest()->getMethod() === 'POST' ? 'Write' : 'Read';

			$action['methods'][] = sprintf('execute%s', $action['action']);
			$action['methods'][] = $action['method'] = 'execute';

			foreach($action['methods'] as $method) {
				if($action['reflection']->hasMethod($method)) {
					$action['method'] = $method;
					break;
				}
			}

			// TODO: Pass arguments to the action constructor.
			$action['instance'] = $action['reflection']->newInstance();

			$config->set('view.name', sprintf(
				'%s%s',
				$config->get('action.name'),
				call_user_func_array(array($action['instance'], $action['method']), array())
			));

			// TODO: Implement support for namespace fallback.
			// If no namespace have been supplied for the specific module, the
			// application namespace should be used. If no namespace have been
			// supplied for the application, global namespace should be used.

			$config->set('view.class', $config->get('view.name'));
			if($config->has('module.namespace')) {
				$config->set('view.class', $config->expandDirectives('%module.namespace%\\%module.name%\\View\\%view.name%'), TRUE);
			}

			$view['reflection'] = new \ReflectionClass($config->get('view.class'));

			// TODO: Implement support for content types.
			// TODO: Handle content type from routing.
			$headers = $this->getRequest()->getHttpHeaders();
			if(isset($headers['content-type'])) {
				$headers['content-type'] = strtolower($headers['content-type']);

				switch($headers['content-type']) {
					case 'application/json':
						$contentType = 'json';
						break;
					case 'text/html':
					default:
						$contentType = 'html';
						break;
				}

				$view['methods'][] = sprintf('execute%s', ucfirst(strtolower($contentType)));
			}
			$view['methods'][] = $view['method'] = 'execute';

			foreach($view['methods'] as $method) {
				if($view['reflection']->hasMethod($method)) {
					$view['method'] = $method;
					break;
				}
			}

			// TODO: Pass arguments to the view constructor.
			$view['instance'] = $view['reflection']->newInstance();
			call_user_func_array(array($view['instance'], $view['method']), array());

			// TODO: Handle loading of template for content type HTML.
		}
	}
}
// End of file: Web.class.php
// Location: library/controller/Web.class.php