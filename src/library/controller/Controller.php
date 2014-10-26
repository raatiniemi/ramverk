<?php
namespace Me\Raatiniemi\Ramverk;

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
    private $classes;

    /**
     * Reflections of class instances.
     * @var array
     */
    private $reflections;

    /**
     * Initialize the controller.
     * @param Me\Raatiniemi\Ramverk\Core\Context $ct Application context.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function __construct(Core\Context $context)
    {
        $this->setContext($context);

        $this->classes = array();
        $this->reflections = array();
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
    public function createInstance($name, array $arguments = array())
    {
        // Check if the reflection already have been instansiated.
        if (!$this->hasReflection($name)) {
            // Check that the class is registered.
            if (!$this->hasClass($name)) {
                throw new \Exception(sprintf(
                    'Class for "%s" have not been defined',
                    $name
                ));
            }

            // Check that the class actually exists. If the class do not
            // exists, the 'class_exists' function will attempt to load it.
            $class = $this->getClass($name);
            if (!class_exists($class)) {
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
        if (!$this->hasReflection($name)) {
            $this->reflections[$name] = $reflection;
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
        if (!$this->hasReflection($name)) {
            throw new \Exception(sprintf(
                'Reflection "%s" have not been instansiated',
                $name
            ));
        }

        return $this->reflections[$name];
    }

    /**
     * Retrieve all of the instansiated reflections.
     * @return array All of the instansiated reflections.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function getReflections()
    {
        // Verify that the reflection have been instansiated.
        if (!is_array($this->reflections)) {
            $this->reflections = array();
        }

        return $this->reflections;
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
        if (!$this->hasClass($name)) {
            $this->classes[$name] = $class;
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
        if (!$this->hasClass($name)) {
            throw new \Exception(sprintf(
                'Class "%s" have not been registered',
                $name
            ));
        }

        return $this->classes[$name];
    }

    /**
     * Retrieve all of the registered classes.
     * @return array All of the registered classes.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function getClasses()
    {
        // Check that the classes actually is an array.
        if (!is_array($this->classes)) {
            $this->classes = array();
        }

        return $this->classes;
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

    /**
     * Initialize the module, setup the directory structure, import configuration, etc.
     * @param string $module Name of the module.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function initializeModule($module)
    {
        // Setup the directory structure for the module.
        $config = $this->getConfig();
        $config->set('directory.module', "%directory.application.module%/{$module}");
        $config->set('directory.module.action', '%directory.module%/action');
        $config->set('directory.module.config', '%directory.module%/config');
        $config->set('directory.module.view', '%directory.module%/view');

        // Check that the module directory exists and is readable.
        $directory = Configuration\Utility::expand($config, '%directory.module%');
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new Exception(sprintf(
                'The directory for the module "%s" do not exists or is '.
                'not readable. Verify that the module exists and check '.
                'the permissions',
                $module
            ));
        }

        // Import the module specific configuration if available.
        $module = '%directory.module.config%/module.xml';
        $module = Configuration\Utility::expand($config, $module);
        if (is_readable($module)) {
            $factory = $this->getConfigurationHandlerFactory();
            $config->fromArray($factory->callHandler('Module', $module));
        }

        // Check if module specific autoload configuration is available.
        $autoload = '%directory.module.config%/autoload.xml';
        $autoload = Configuration\Utility::expand($config, $autoload);
        if (is_readable($autoload)) {
            $this->setAutoloadFile($autoload);
            spl_autoload_register(array($this, 'autoload'), true, true);
        }
    }

    public function dispatch()
    {
        // Retrieve the configuration container and the configuration handler factory.
        $config = $this->getConfig();
        $factory = $this->getConfigurationHandlerFactory();

        // Import the application core configurations.
        $config->fromArray($factory->callHandler('Core', '%directory.application.config%/core.xml'));
    }
}
// End of file: Controller.php
// Location: library/controller/Controller.php
