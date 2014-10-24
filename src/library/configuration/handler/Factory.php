<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;
use Me\Raatiniemi\Ramverk\Configuration;
use Me\Raatiniemi\Ramverk\Data\Dom;
use Me\Raatiniemi\Ramverk\Utility;

/**
 * Factory for instansiating configuration handlers.
 *
 * @package Ramverk
 * @subpackage Configuration
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Factory
{
    // +------------------------------------------------------------------+
    // | Trait use-directives.                                            |
    // +------------------------------------------------------------------+
    use Configuration\Utility;
    use Utility\Filesystem;

    /**
     * Stores the available configuration handlers.
     * @var array
     */
    protected $availableHandlers = array();

    /**
     * Stores instansiated configuration handlers.
     * @var array
     */
    protected $handlers = array();

    /**
     * Configuration container.
     * @var Me\Raatiniemi\Ramverk\Configuration
     */
    protected $config;

    /**
     * Caching for configuration handler data.
     * @var Me\Raatiniemi\Ramverk\Configuration\Cache
     */
    protected $cache;

    /**
     * Parser for configuration handlers.
     * @var Me\Raatiniemi\Ramverk\Configuration\Parser
     */
    protected $parser;

    /**
     * Initialize the configuration handler factory.
     * @param Me\Raatiniemi\Ramverk\Configuration $config Configuration container.
     * @param Me\Raatiniemi\Ramverk\Configuration\Handler\Cache $cache Caching for configuration handler data.
     * @param Me\Raatiniemi\Ramverk\Configuration\Handler\Parser $parser Parser for configuration handlers.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function __construct(Configuration $config, Cache $cache, Parser $parser)
    {
        $this->config = $config;
        $this->cache = $cache;
        $this->parser = $parser;
    }

    /**
     * Call the configuration handler.
     * @param string $name Name of the configuration handler.
     * @return array Parsed configuration from the handler.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     * @todo Find a better way to mock the $file and $cache objects.
     */
    public function callHandler($name, $filename)
    {
        // Get the absolute path for the configuration file.
        $filename = $this->expandDirectives($filename);

        // Check that the configuration directory exists.
        if (!$this->isDir(dirname($filename))) {
            // TODO: Better specify the Exception-object.
            throw new Ramverk\Exception(sprintf(
                'Configuration directory "%s" do not exists.',
                dirname($filename)
            ));
        }

        // Check that the configuration file do exists and is readable.
        if (!$this->isReadable($filename) || !$this->isFile($filename)) {
            // TODO: Better specify the Exception-object.
            throw new Ramverk\Exception(sprintf(
                'The specified configuration file "%s" do not exists.',
                basename($filename)
            ));
        }

        // Instansiate the configuration file with the filename.
        $file = new Utility\File($filename);

        // Generate the cachename, and prepend the absolute cache directory.
        $cachename = $this->expandDirectives(sprintf(
            '%s/%s',
            '%directory.application.cache%',
            $this->cache->generateName($file)
        ));

        // Instansiate the cache file with the generated cachename.
        $cache = new Utility\File($cachename);

        // Check if the configuration file is available from the cache.
        // There's no need to load and instansiate the configuration handler
        // if we don't really need to.
        if ($this->cache->isModified($file, $cache)) {
            // Check if the specified handler is available.
            if (!$this->hasHandler($name)) {
                // TODO: Better specify the Exception-object.
                throw new Ramverk\Exception(sprintf(
                    'The configuration handler "%s" is not registered.',
                    $name
                ));
            }

            // Retrieve the class name for the configuration handler.
            // The class name also includes the full namespace.
            $handler = $this->availableHandlers[$name];

            // Check if the handler have been instansiated/loaded. There's
            // no need to instansiate a handler more than once.
            if (!$this->isInstansiated($handler)) {
                $this->loadHandler($handler);
            }

            // Retrieve the handler-instance.
            $instance = $this->handlers[$handler];

            // Setup the arguments for the handlers execute-method.
            $document = new Dom\Document('1.0', 'UTF-8');
            $document->load($file->getPathname());

            // Parse the configuration document. Parsing includes inclusion
            // of possible parent documents, retrieval of configuration
            // connected to the correct application profile and context.
            $document = $this->parser->execute($document);
            $arguments = array($document, $this->config);

            // Call the execute-method on the handler. The execute-method will
            // retrieve all of the configuration data within one array.
            $data = call_user_func_array(array($instance, 'execute'), $arguments);
            if (!is_array($data)) {
                // TODO: Better specify the Exception-object.
                throw new Ramverk\Exception(sprintf(
                    'The configuration handler "%s" did not return an array.',
                    $handler
                ));
            }

            // Cache the retrieved configuration data.
            $this->cache->write($cache, $data);
        } else {
            // Retrieve the cached configuration data.
            $data = $this->cache->read($cache);
        }

        // Return the retrieved configuration data.
        return $data;
    }

    /**
     * Instansiates/loads the configuration handler.
     * @param string $handler Handlers full class name, with namespace.
     * @return void Handlers are stored within the handlers array.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    protected function loadHandler($handler)
    {
        // Check that the handler class actually exists. If it isn't loaded
        // yet the autoload functionality will attempt to include it.
        if (!class_exists($handler)) {
            // TODO: Better specify the Exception-object.
            throw new Ramverk\Exception(sprintf(
                'The configuration handler "%s" do not exists.',
                $handler
            ));
        }

        // Seems as though the handler class exists. We need to check
        // a few things before instansiating the class.
        $reflection = new \ReflectionClass($handler);

        // The handler have to extend the Handler base.
        $base = __NAMESPACE__;
        if (!$reflection->isSubclassOf($base)) {
            // TODO: Better specify the Exception-object.
            throw new Ramverk\Exception(sprintf(
                'The configuration handler "%s" do not extend the base '.
                'configuration handler.',
                $handler
            ));
        }

        // Everything seems good to go, instansiate the handler.
        $this->handlers[$handler] = $reflection->newInstanceArgs(array(
            $this->getConfig()
        ));
    }

    /**
     * Check if specified configuration handler is registered.
     * @param string $name Name of the handler.
     * @return boolean True if handler is registered, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function hasHandler($name)
    {
        return array_key_exists($name, $this->availableHandlers)
            && isset($this->availableHandlers[$name]);
    }

    /**
     * Check if the specified configuration handler is instansiated/loaded.
     * @param string $handler Handlers full class name, with namespace.
     * @return boolean True if handler is instansiated, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    protected function isInstansiated($handler)
    {
        return array_key_exists($handler, $this->handlers)
            && isset($this->handlers[$handler]);
    }

    /**
     * Register configuration handler.
     * @param string $name Name of the handler, used with callHandler.
     * @param string $class Handlers full class name, with namespace.
     * @throws Me\Raatiniemi\Ramverk\Exception If handler is already registered.
     * @return boolean True if handler is successfully registered.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function registerHandler($name, $class)
    {
        // Check that the configuration handler have not already been
        // registered. We don't want to override existing handlers.
        if ($this->hasHandler($name)) {
            throw new Ramverk\Exception(sprintf(
                'The configuration handler "%s" have already been registered.',
                $name
            ));
        }

        // Register the configuration handler.
        $this->availableHandlers[$name] = $class;
        return true;
    }

    /**
     * Get the configuration container, used by Utility-trait.
     * @return Me\Raatiniemi\Ramverk\Configuration\Container Configuration container.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     * @codeCoverageIgnore
     */
    public function getConfig()
    {
        return $this->config;
    }
}
// End of file: Factory.php
// Location: library/configuration/handler/Factory.php
