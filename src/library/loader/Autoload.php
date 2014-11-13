<?php
namespace Me\Raatiniemi\Ramverk\Loader;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;
use Me\Raatiniemi\Ramverk\Configuration\Handler;

/**
 * Handles autoloading of classes, interfaces and traits.
 *
 * @package Ramverk
 * @subpackage Loader
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
trait Autoload
{
    /**
     * Items available for autoloading.
     * @var array
     */
    private $autoload;

    /**
     * Initialize the autoload functionality.
     * @param Me\Raatiniemi\Ramverk\Configuration\Handler\Factory $factory Configuration factory.
     * @param string $filename Autoload configuration file.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function initializeAutoload(Handler\Factory $factory, $filename)
    {
        // Check if the autoload already have been initialized.
        if (!empty($this->autoload)) {
            // TODO: Write exception message.
            // TODO: Better specify the exception object.
            throw new Ramverk\Exception('');
        }

        // Attempt to retrieve the autoload configuration.
        $autoload = $factory->callHandler('Autoload', $filename);
        if (empty($autoload)) {
            // TODO: Write exception message.
            // TODO: Better specify the exception object.
            throw new Ramverk\Exception('');
        }
        $this->autoload = $autoload;

        // Register the class as an autoloader, prepend the autoloader to the queue.
        spl_autoload_register(array($this, 'autoload'), true, true);
    }

    /**
     * Handles autoloading of classes, interfaces and traits.
     * @param string $name Name of the item to autoload, with namespace.
     * @return boolean True if item was loaded, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function autoload($name)
    {
        // Check if the class exists within our autoload configurations.
        if (isset($this->autoload[$name])) {
            // Before attempting to include the file, we have to check that
            // it actually exists otherwise we'll get errors.
            if (!file_exists($this->autoload[$name])) {
                // TODO: Better specify the Exception-object.
                throw new Ramverk\Exception(sprintf(
                    'Class "%s" is specified within autoload '.
                    'configuration but do not exists.',
                    $name
                ));
            }

            // Since autoload is not triggered unless the class do not
            // exists there is no need for require_once, or other checks.
            require $this->autoload[$name];
        }

        // If we do not find the class there's nothing we can do. If we
        // throw an exception or trigger an error we might prevent other
        // autoloaders of finding the item.
        //
        // E.g. if the module autoloader do not finds the item because it
        // is located within the core autoload items.
        //
        // Do not trigger a new autoload-chain with the controll.
        return class_exists($name, false);
    }
}
// End of file: Autoload.php
// Location: library/loader/Autoload.php
