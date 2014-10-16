<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;
use Me\Raatiniemi\Ramverk\Configuration;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Handler for module configuration.
 *
 * @package Ramverk
 * @subpackage Configuration
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Module extends Configuration\Handler
{
    /**
     * Execute the configuration handler.
     * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
     * @throws Me\Raatiniemi\Ramverk\Exception If setting item is missing name attribute.
     * @return array Retrieved configuration data.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function execute(Dom\Document $document)
    {
        // Iterate over the configuration and retrieve the system actions and settings.
        $data = array();
        foreach ($document->getConfigurationElements() as $configuration) {
            foreach ($configuration->get('settings') as $settings) {
                foreach ($settings->get('setting') as $setting) {
                    // Every setting item must have the name attribute.
                    if (!$setting->hasAttribute('name')) {
                        // TODO: Write exception message.
                        // TODO: Better specify the exception object.
                        throw new Ramverk\Exception('');
                    }

                    // Retrieve the setting name and value.
                    $name = $setting->getAttribute('name');
                    $data["module.{$name}"] = $this->expandDirectives($setting->getValue());
                }
            }
        }
        return $data;
    }
}
// End of file: Module.php
// Location: library/configuration/handler/Module.php
