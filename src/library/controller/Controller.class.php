<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Functionality for dispatching actions.
	 *
	 * @package Ramverk
	 * @subpackage Controller
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	final class Controller
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Core\Context\Handler;
		use Loader\Autoload;

		/**
		 * Registered classes with their name.
		 * @var array
		 */
		private $_classes;

		/**
		 * Reflections of class instances.
		 * @var array
		 */
		private $_reflections;

		/**
		 * Initialize the controller.
		 * @param Me\Raatiniemi\Ramverk\Core\Context $ct Application context.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct(Core\Context $ct)
		{
			$this->setContext($ct);

			$this->_classes = array();
			$this->_reflections = array();
		}

		/**
		 * Creates an instance of a registered class.
		 * @param string $name Name of the class to instansiate.
		 * @param array $arguments Arguments to send to the class constructor.
		 * @throws Exception If class name have not been registered.
		 * @throws Exception If class can not be found.
		 * @return Instance of the class.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function createInstance($name, array $arguments=array())
		{
			// Check if the reflection already have been instansiated.
			if(!$this->hasReflection($name)) {
				// Check that the class is registered.
				if(!$this->hasClass($name)) {
					throw new \Exception(sprintf(
						'Class for "%s" have not been defined',
						$name
					));
				}

				// Check that the class actually exists. If the class do not
				// exists, the 'class_exists' function will attempt to load it.
				$class = $this->getClass($name);
				if(!class_exists($class)) {
					throw new \Exception(sprintf(
						'Class "%s" can not be located',
						$class
					));
				}
				// Instansiate the reflection of the class and store it.
				$reflection = new \ReflectionClass($class);
				$this->setReflection($name, $reflection);
			}
			// Retrieve the class reflection and create a new instance.
			return $this->getReflection($name)->newInstanceArgs($arguments);
		}

		/**
		 * Register a new reflection with a name.
		 * @param string $name Name of the reflection to register.
		 * @param ReflectionClass $reflection Reflection to register.
		 */
		public function setReflection($name, \ReflectionClass $reflection)
		{
			// Check if the reflection already have been instansiated.
			if(!$this->hasReflection($name)) {
				$this->_reflections[$name] = $reflection;
			} else {
				// TODO: Reflection is already defined, handle it.
			}
		}

		/**
		 * Retrieve a instansiated reflection based on the name.
		 * @param string $name Name of the instansiated reflection.
		 * @return ReflectionClass Reflection of the class.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getReflection($name)
		{
			// Verify that the reflection have been instansiated.
			if(!$this->hasReflection($name)) {
				throw new \Exception(sprintf(
					'Reflection "%s" have not been instansiated',
					$name
				));
			}

			return $this->_reflections[$name];
		}

		/**
		 * Retrieve all of the instansiated reflections.
		 * @return array All of the instansiated reflections.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getReflections()
		{
			// Verify that the reflection have been instansiated.
			if(!is_array($this->_reflections)) {
				$this->_reflections = array();
			}

			return $this->_reflections;
		}

		/**
		 * Check whether a reflection have been instansiated.
		 * @param string $name Name of the reflection.
		 * @return boolean True if reflection have been instansiated, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function hasReflection($name)
		{
			$reflections = $this->getReflections();
			return isset($reflections[$name]);
		}

		/**
		 * Register a new class with a name.
		 * @param string $name Name of the class to register.
		 * @param string $class Full class path for the class, including namespace.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function setClass($name, $class)
		{
			// Check if the class already have been registered.
			if(!$this->hasClass($name)) {
				$this->_classes[$name] = $class;
			} else {
				// TODO: Class is already defined, handle it.
			}
		}

		/**
		 * Retrieve a registered class based on the name.
		 * @param string $name Name of the registered class.
		 * @throws Exception If class is not registered.
		 * @return string Full class path for the class, including namespace.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getClass($name)
		{
			// Verify that the class have been registered.
			if(!$this->hasClass($name)) {
				throw new \Exception(sprintf(
					'Class "%s" have not been registered',
					$name
				));
			}

			return $this->_classes[$name];
		}

		/**
		 * Retrieve all of the registered classes.
		 * @return array All of the registered classes.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getClasses()
		{
			// Check that the classes actually is an array.
			if(!is_array($this->_classes)) {
				$this->_classes = array();
			}

			return $this->_classes;
		}

		/**
		 * Check if a class have been registered.
		 * @param string $name Name for the registered class.
		 * @return boolean True if class have been registered, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function hasClass($name)
		{
			$classes = $this->getClasses();
			return isset($classes[$name]);
		}

		public function dispatch()
		{
		}
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php