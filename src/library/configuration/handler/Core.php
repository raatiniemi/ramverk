<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;
use Me\Raatiniemi\Ramverk\Configuration;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Handler for core application configuration.
 *
 * @package Ramverk
 * @subpackage Configuration
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Core extends Configuration\Handler
{
    /**
     * Execute the configuration handler.
     * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
     * @throws Me\Raatiniemi\Ramverk\Exception If system action is missing name attribute.
     * @throws Me\Raatiniemi\Ramverk\Exception If system action is missing module item.
     * @throws Me\Raatiniemi\Ramverk\Exception If the value for the system action module item is invalid.
     * @throws Me\Raatiniemi\Ramverk\Exception If system action is missing action item.
     * @throws Me\Raatiniemi\Ramverk\Exception If the value for the system action action item is invalid.
     * @throws Me\Raatiniemi\Ramverk\Exception If setting item is missing name attribute.
     * @throws Me\Raatiniemi\Ramverk\Exception If module or action have not been defined for the default system action.
     * @throws Me\Raatiniemi\Ramverk\Exception If module or action have not been defined for the 404 system action.
     * @return array Retrieved configuration data.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function execute(Dom\Document $document)
    {
        // Iterate over the configuration and retrieve the system actions and settings.
        $data = array();
        foreach ($document->getConfigurationElements() as $configuration) {
            $data = array_merge(
                $data,
                // Attempt to retrieve the system action configuration.
                $this->getSystemActions($configuration),
                // Attempt to retrieve the core application configuration.
                $this->getSettings($configuration)
            );
        }

        // Verify that the default and 404 system actions have been defined.
        foreach (array('actions.default', 'actions.404') as $prefix) {
            if (!isset($data["{$prefix}_module"], $data["{$prefix}_action"])) {
                // TODO: Write exception message.
                // TODO: Better specify the exception object.
                throw new Ramverk\Exception('');
            }
        }
        return $data;
    }

    /**
     * Retrieve the system action configurations.
     * @param Me\Raatiniemi\Ramverk\Data\Dom\Element $configuration Configuration element.
     * @throws Me\Raatiniemi\Ramverk\Exception If system action is missing name attribute.
     * @throws Me\Raatiniemi\Ramverk\Exception If system action is missing module item.
     * @throws Me\Raatiniemi\Ramverk\Exception If the value for the system action module item is invalid.
     * @throws Me\Raatiniemi\Ramverk\Exception If system action is missing action item.
     * @throws Me\Raatiniemi\Ramverk\Exception If the value for the system action action item is invalid.
     * @return array Array with system action configuration.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    private function getSystemActions(Dom\Element $configuration)
    {
        $data = array();

        // Check whether the configuration have `system_actions` defined.
        if ($configuration->hasChild('system_actions')) {
            $actions = $configuration->getChild('system_actions');
            if ($actions->hasChildren('system_action')) {
                foreach ($actions->getChildren('system_action') as $item) {
                    // Every system action must have the name defined.
                    if (!$item->hasAttribute('name')) {
                        // TODO: Write exception message.
                        // TODO: Better specify the exception object.
                        throw new Ramverk\Exception('');
                    }

                    // Every system action must have the module item defined.
                    if (!$item->hasChild('module')) {
                        // TODO: Write exception message.
                        // TODO: Better specify the exception object.
                        throw new Ramverk\Exception('');
                    }

                    // Check that the module-item contains a value.
                    // Can't depend on the internal hasValue-method for the DOM
                    // node, since it'd only check for empty string and zero
                    // based values is not valid modules.
                    //
                    // TODO: Add regexp for module names?
                    $module = $item->getChild('module')->getValue();
                    if (empty($module)) {
                        // TODO: Write exception message.
                        // TODO: Better specify the exception object.
                        throw new Ramverk\Exception('');
                    }

                    // Every system action must have the action item defined.
                    if (!$item->hasChild('action')) {
                        // TODO: Write exception message.
                        // TODO: Better specify the exception object.
                        throw new Ramverk\Exception('');
                    }

                    // Check that the action-item contains a value.
                    // Can't depend on the internal hasValue-method for the DOM
                    // node, since it'd only check for empty string and zero
                    // based values is not valid modules.
                    //
                    // TODO: Add regexp for action names?
                    $action = $item->getChild('action')->getValue();
                    if (empty($action)) {
                        // TODO: Write exception message.
                        // TODO: Better specify the exception object.
                        throw new Ramverk\Exception('');
                    }

                    // Assemble the system action with module and action.
                    $name = strtolower($item->getAttribute('name'));
                    $data["actions.{$name}_module"] = $module;
                    $data["actions.{$name}_action"] = $action;
                }
            }
        }
        // Return the retrieved system action configurations.
        return $data;
    }

    /**
     * Retrieve the core application settings.
     * @param Me\Raatiniemi\Ramverk\Data\Dom\Element $configuration Configuration element.
     * @throws Me\Raatiniemi\Ramverk\Exception If setting item is missing name attribute.
     * @return array Array with the core application settings.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    private function getSettings(Dom\Element $configuration)
    {
        $data = array();

        // Check whether the configuration have `settings` defined.
        if ($configuration->hasChild('settings')) {
            $settings = $configuration->getChild('settings');
            if ($settings->hasChildren('setting')) {
                foreach ($settings->getChildren('setting') as $setting) {
                    // Settings have to be defined with a name.
                    if (!$setting->hasAttribute('name')) {
                        // TODO: Write exception message.
                        // TODO: Better specify the exception object.
                        throw new Ramverk\Exception('');
                    }

                    // Retrieve the value for the setting.
                    // It is allowed for the setting value to be 'null', so no
                    // need to actually check the value.
                    $name = strtolower($setting->getAttribute('name'));
                    $data["core.{$name}"] = $setting->getValue();
                }
            }
        }
        // Return the retrieved settings.
        return $data;
    }
}
// End of file: Core.php
// Location: library/configuration/handler/Core.php
