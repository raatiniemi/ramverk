<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler
{
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
	class Core extends Configuration\Handler
	{
		/**
		 * Execute the configuration handler.
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document)
		{
			$data = array();
			foreach($document->getConfigurationElements() as $configuration) {
				// Check whether the configuration have `system_actions` defined.
				if($configuration->hasChild('system_actions')) {
					$actions = $configuration->getChild('system_actions');
					if($actions->hasChildren('system_action')) {
						foreach($actions->getChildren('system_action') as $action) {
							// The system action have to be defined with a name.
							if(!$action->hasAttribute('name')) {
								// TODO: Throw exception, no name system action is not allowed.
							}

							// The pre-defined system action have to define a module.
							if($action->hasChild('module')) {
								// TODO: Throw exception, system action without defined module is not allowed.
							}

							// The pre-defined system action have to define a action.
							if($action->hasChild('action')) {
								// TODO: Throw exception, system action without defined action is not allowed.
							}

							// Retrieve the module and action for the system action.
							$name = strtolower($action->getAttribute('name'));
							$data["actions.{$name}_module"] = $action->getChild('module')->getValue();
							$data["actions.{$name}_action"] = $action->getChild('action')->getValue();
						}
					}
				}

				// Check whether the configuration have `settings` defined.
				if($configuration->hasChild('settings')) {
					$settings = $configuration->getChild('settings');
					if($settings->hasChildren('setting')) {
						foreach($settings->getChildren('setting') as $setting) {
							// Settings have to be defined with a name.
							if($setting->hasAttribute('name')) {
								// TODO: Throw exception, setting without name is not allowed.
							}

							// Retrieve the value for the setting.
							$name = strtolower($setting->getAttribute('name'));
							$data["core.{$name}"] = $setting->getValue();
						}
					}
				}
			}
			// TODO: Validate the configuration data.
			// Module and action for the default and 404 system action have to be defined.
			return $data;
		}
	}
}
// End of file: Core.class.php
// Location: library/configuration/handler/Core.class.php