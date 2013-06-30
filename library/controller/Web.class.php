<?php
namespace Net\TheDeveloperBlog\Ramverk\Controller
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Controller for web requests.
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
		// TODO: Move initializeModule to parent controller.
		protected function initializeModule($name)
		{
			// Assemble and expand the module directory.
			$directory = "%directory.application.module%/{$name}";
			$directory = $this->_config->expandDirectives($directory);

			// Verify that the module directory actually exists.
			if(!is_dir($directory)) {
				throw new Ramverk\Exception(sprintf(
					'Module "%s" do not exists.',
					$name
				));
			}

			$handlerFactory = $this->_core->getHandlerFactory();

			// Load the module configurations.
			$filename = "{$directory}/config/module.xml";
			$module = $handlerFactory->callHandler('Module', $filename);

			$filename = "{$directory}/config/autoload.xml";
			if(file_exists($filename)) {
				$autoload = $handlerFactory->callHandler('Autoload', $filename);

				// TODO: Merge the configurations.
			}

			// TODO: Use trait.

			// TODO: Read the module configuration.
			// We need the module namespace, autoloads etc.
			// $this->_moduleNamespace.

			// TODO: Controller autoloader for module classes?
			// Or merge config with core autoloader.
		}

		public function dispatch()
		{
			$this->initializeModule($this->_routing->getModule());

			// $httpMethod = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';
			//
			// To be able to handle every header key, regardless of its original
			// case we have to change it to lowercase.
			// $httpHeaders = array_change_key_case(getallheaders());
			// $contentType = 'html'; // Default content type.

			// if(isset($httpHeaders['content-type'])) {
			// 	switch($httpHeaders['content-type']) {
			// 		case 'application/json':
			// 			$contentType = 'json';
			// 			break;
			// 		default:
			// 			$contentType = 'html';
			// 	}
			// }
			// $contentType = ucfirst(strtolower($contentType));
			//
			// TODO: Check that the action exists.
			// TODO: Instansiate a reflection of the action.
			// We need to determind if the action have the POST/GET and
			// content type based methods.
			//
			// ------------- Action -------------
			// - POST
			// ExecuteJsonWrite
			// ExecuteXmlWrite
			// ExecuteWrite
			// - GET
			// ExecuteJsonRead
			// ExecuteHtmlRead
			// ExecuteRead
			// Execute
			//
			// The same have to be done with the View.
			// ------------- View -------------
			// Views do not need write/read specific since every method
			// is read. The writing is done within the action.
			//
			// ExecuteJson
			// ExecuteHtml
			// ExecuteXml
			// Execute
			//
			// Create class reflection to check for methods.
			// execute, executeRead, executeWrite.
			//
			// Returns e.g. success, which means the view class that should be
			// invoked is {$actionName}Success, and the template that should be
			// loaded is {$actionName}Success.
			//
			// Instansiate the reflection of the action class. We need to determind
			// that the action method actually exists, default is execute.
			// $actionReflection = new \ReflectionClass($action);
			//
			// $actionHttpMethod = $httpMethod === 'POST' ? 'Write' : 'Read';
			// $actionMethods[] = sprintf('execute%s%s', $contentType, $actionHttpMethod);
			// $actionMethods[] = sprintf('execute%s', $actionHttpMethod);
			// $actionMethods[] = $actionMethod = 'execute';
			//
			// foreach($actionMethods as $method) {
			// 	if($actionReflection->hasMethod($method)) {
			// 		$actionMethod = $method;
			// 		break;
			// 	}
			// }
			//
			// // TODO: Pass arguments to the action constructor.
			// $actionInstance = $actionReflection->newInstance();
			// $viewName = call_user_func_array(array($actionInstance, $actionMethod), array());
			//
			// $view = sprintf($namespace, 'View', sprintf('%s%s', ucfirst($actionName), ucfirst($viewName)));
			// if(!class_exists($view)) {
			// 	// TODO: Throw an 404-exception.
			// 	throw new \Exception('View not found.');
			// }
			//
			// $viewReflection = new \ReflectionClass($view);
			//
			// $viewMethods[] = sprintf('execute%s', $contentType);
			// $viewMethods[] = $viewMethod = 'execute';
			//
			// foreach($viewMethods as $method) {
			// 	if($viewReflection->hasmethod($method)) {
			// 		$viewMethod = $method;
			// 		break;
			// 	}
			// }
			//
			// TODO: Pass arguments to the view constructor.
			// $viewInstance = $viewReflection->newInstance();
			// call_user_func_array(array($viewInstance, $viewMethod), array());
			//
			//
			// TODO: Load template depending on value returned from action.
		}
	}
}
// End of file: Web.class.php
// Location: library/controller/Web.class.php