<?php
namespace Me\Raatiniemi\Ramverk;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

/**
 * Initialize and setup the core functionality for the framework.
 *
 * @package Ramverk
 * @subpackage Core
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
final class Core
{
    // +------------------------------------------------------------------+
    // | Trait use-directives.                                            |
    // +------------------------------------------------------------------+
    use Core\Context\Handler;
    use Loader\Autoload;

    /**
     * Application controller.
     * @var Me\Raatiniemi\Ramverk\Controller
     */
    private $controller;

    /**
     * Initialize the ramverk core.
     * @param Me\Raatiniemi\Ramverk\Configuration Configuration container.
     * @param string $profile Profile for the application, optional.
     * @param string $context Context for the application, optional.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function __construct(Configuration $config, $profile = null, $context = null)
    {
        // Check if the configuration container have been supplied with a profile.
        if (!$config->has('profile')) {
            // If no profile have been defined, check if the profile have been
            // supplied as an argument, otherwise use the default profile.
            $profile = isset($profile) ? $profile : $config->get('profile.default', 'development');
            $config->set('profile', $profile);
        }

        // Check if the configuration container have been supplied with a context.
        if (!$config->has('context')) {
            // If no context have been defined, check if the context have been
            // supplied as an argument, otherwise use the default context.
            $context = isset($context) ? $context : $config->get('context.default', 'web');
            $config->set('context', $context);
        }

        // TODO: Validate the retrieved context name.
        // Since each of the context requires certain classes and
        // configurations we can only allow for specific context names to
        // be used, i.e. 'web', 'console', etc.
        //
        // However, it should be possible to supply additional context
        // names through the application configuration.

        // Check whether the default exception template have been defined.
        if (!$config->has('exception.template.default')) {
            $config->set('exception.template.default', '%directory.core.template%/exception/plaintext.php');
        }

        // Setup the default directory structure.
        $this->setupDirectories($config);

        // Initialize the core context.
        // TODO: Implement support for custom core context classes?
        $this->setContext(new Core\Context($config));

        // Register the configuration handlers.
        $this->registerConfigurationHandlers();

        // Register the framework autoload handler.
        $factory = $this->getConfigurationHandlerFactory();
        $this->initializeAutoload($factory, '%directory.application.config%/autoload.xml');
    }

    /**
     * Setup the default directory structure.
     *
     * If directories already have been specified, the directories will not be replaced.
     *
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     * @todo Implement check for has directory before attempting to write it.
     */
    private function setupDirectories($config)
    {
        // -- Core directory structure.
        // Check that a base core directory have been supplied.
        if (!$config->has('directory.core')) {
            // TODO: Better exception message, include config name.
            // TODO: Better specify the Exception-object.
            throw new Exception('No core directory have been supplied.');
        }

        // Setup the default directory structure for the core.
        $config->set('directory.core.config', '%directory.core%/config');
        $config->set('directory.core.library', '%directory.core%/library');
        $config->set('directory.core.template', '%directory.core%/template');

        // -- Application directory structure.
        // Check that a base application directory have been supplied.
        if (!$config->has('directory.application')) {
            // TODO: Better exception message, include config name.
            // TODO: Better specify the Exception-object.
            throw new Exception('No application directory have been supplied.');
        }

        // Setup the default directory structure for the application.
        $config->set('directory.application.cache', '%directory.application%/cache');
        $config->set('directory.application.config', '%directory.application%/config');
        $config->set('directory.application.library', '%directory.application%/library');
        $config->set('directory.application.module', '%directory.application%/module');
        $config->set('directory.application.template', '%directory.application%/template');
    }

    /**
     * Register the configuration handlers.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    private function registerConfigurationHandlers()
    {
        // Base namespace for the configuration handlers.
        $namespace = 'Me\\Raatiniemi\\Ramverk\\Configuration\\Handler';

        // Register the handlers with the handler factory.
        $factory = $this->getConfigurationHandlerFactory();
        $factory->registerHandler('Autoload', "{$namespace}\\Autoload");
        $factory->registerHandler('Core', "{$namespace}\\Core");
        $factory->registerHandler('Module', "{$namespace}\\Module");
        $factory->registerHandler('Routing', "{$namespace}\\Routing");
    }

    /**
     * Retrieve the application controller.
     * @return Me\Raatiniemi\Ramverk\Controller Application controller.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function getController()
    {
        // The application controller should only be initialized once.
        if ($this->controller === null) {
            // Check that the controller class actually exists, and attempt
            // to autoload it the event it do not exists.
            $name = 'Me\\Raatiniemi\\Ramverk\\Controller';
            if (!class_exists($name)) {
                throw new Exception('Class for the application controller could not be found');
            }

            // Initialize the class reflection and instansiate the controller.
            $reflection = new \ReflectionClass($name);
            $this->controller = $reflection->newInstanceArgs(array($this->getContext()));
        }

        return $this->controller;
    }
}
// End of file: Core.php
// Location: library/core/Core.php
