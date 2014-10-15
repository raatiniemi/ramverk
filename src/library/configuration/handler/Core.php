<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Configuration;
	use Me\Raatiniemi\Ramverk\Data\Dom;

	/**
	 * Handler for core configuration.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Core extends Configuration\Handler {
		/**
		 * Execute the configuration handler.
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document) {
			$data = array();
			foreach($document->getConfigurationElements() as $configuration) {
				// Check whether the configuration have `system_actions` defined.
				if($configuration->hasChild('system_actions')) {
					$actions = $configuration->getChild('system_actions');
					if($actions->hasChildren('system_action')) {
						foreach($actions->getChildren('system_action') as $item) {
							// The pre-defined system action have to be defined with a name.
							if(!$item->hasAttribute('name')) {
								// TODO: Write exception message.
								throw new Ramverk\Exception();
							}

							// The pre-defined system action have to define a module.
							if(!$item->hasChild('module')) {
								// TODO: Write exception message.
								throw new Ramverk\Exception();
							}

							// Retrieve the module for the pre-defined system action.
							$module = $item->getChild('module')->getValue();
							if(empty($module)) {
								// TODO: Write exception message.
								throw new Ramverk\Exception();
							}

							// The pre-defined system action have to define a action.
							if(!$item->hasChild('action')) {
								// TODO: Write exception message.
								throw new Ramverk\Exception();
							}

							// Retrieve the action for the pre-defined system action.
							$action = $item->getChild('action')->getValue();
							if(empty($action)) {
								// TODO: Write exception message.
								throw new Ramverk\Exception();
							}

							// Assemble the system action with module and action.
							$name = strtolower($item->getAttribute('name'));
							$data["actions.{$name}_module"] = $module;
							$data["actions.{$name}_action"] = $action;
						}
					}
				}

				// Check whether the configuration have `settings` defined.
				if($configuration->hasChild('settings')) {
					$settings = $configuration->getChild('settings');
					if($settings->hasChildren('setting')) {
						foreach($settings->getChildren('setting') as $setting) {
							// Settings have to be defined with a name.
							if(!$setting->hasAttribute('name')) {
								// TODO: Write exception message.
								throw new Ramverk\Exception();
							}

							// Retrieve the value for the setting.
							$name = strtolower($setting->getAttribute('name'));
							$data["core.{$name}"] = $setting->getValue();
						}
					}
				}
			}

			// Verify that the default and 404 system actions have been defined.
			foreach(array('actions.default', 'actions.404') as $prefix) {
				if(!isset($data["{$prefix}_module"], $data["{$prefix}_action"])) {
					// TODO: Write exception message.
					throw new Ramverk\Exception();
				}
			}
			return $data;
		}
	}
}
// End of file: Core.php
// Location: library/configuration/handler/Core.php