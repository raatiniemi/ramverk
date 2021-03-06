<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;
use Me\Raatiniemi\Ramverk\Configuration;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Handler for autoload configuration.
 *
 * @package Ramverk
 * @subpackage Configuration
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Autoload extends Configuration\Handler
{
    /**
     * Execute the configuration handler.
     * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
     * @throws Me\Raatiniemi\Ramverk\Exception If an autoload item is missing the name-attribute.
     * @return array Retrieved configuration data.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     * @todo If the group do not have a defined namespace, the application namespace should be used?
     */
    public function execute(Dom\Document $document)
    {
        $data = array();
        foreach ($document->getConfigurationElements() as $configuration) {
            foreach ($configuration->get('autoloads') as $group) {
                // If the autoload group has defined a namespace, then this
                // namespace will prefix every class within the group.
                $namespace = null;
                if ($group->hasAttribute('namespace')) {
                    $namespace = "{$group->getAttribute('namespace')}\\";
                }

                // Retrieve the autoload items from the group.
                foreach ($group->get('autoload') as $autoload) {
                    // Every item must have the name of the class defined.
                    if (!$autoload->hasAttribute('name')) {
                        // TODO: Write exception message.
                        // TODO: Better specify the exception object.
                        throw new Ramverk\Exception('');
                    }

                    // Every item must contain the path to the defined class.
                    if (!$autoload->hasValue()) {
                        // TODO: Write exception message.
                        // TODO: Better specify the exception object.
                        throw new Ramverk\Exception('');
                    }

                    // Prepend the group namespace (if any) to the class
                    // name, and expand the class path directives.
                    $name = "{$namespace}{$autoload->getAttribute('name')}";
                    $data[$name] = Configuration\Utility::expand(
                        $this->getConfig(),
                        $autoload->getValue()
                    );

                    // TODO: Verify that the file actually exists.
                }
            }
        }
        // TODO: Should we throw an exception if $data is empty?
        return $data;
    }
}
// End of file: Autoload.php
// Location: library/configuration/handler/Autoload.php
