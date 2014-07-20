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
			$prefix = 'core';

			$groups = $document->getElementsByTagName('configuration');
			foreach($groups as $group) {
				$actions = $group->getElementsByTagName('system_action');
				if(!empty($actions)) {
					foreach($actions as $action) {
						if(!$action->hasAttribute('name')) {
							// TODO: Throw exception, no name action is not allowed.
						}

						// TODO: Simplify the process of retrieving sub element values.
						// $action->getChild('module')->getValue();
						// Throw exception if the child is not found?
						$name = strtolower($action->getAttribute('name'));
						$data["actions.{$name}_module"] = $action->getElementsByTagName('module')->item(0)->getValue();
						$data["actions.{$name}_action"] = $action->getElementsByTagName('action')->item(0)->getValue();
					}
				}

				$settings = $group->getElementsByTagName('setting');
				if(!empty($settings)) {
					foreach($settings as $setting) {
						if(!$setting->hasAttribute('name')) {
							// TODO: Throw exception, no name action is not allowed.
						}

						// TODO: Add support for local prefix.
						$name = strtolower($setting->getAttribute('name'));
						$data["{$prefix}.{$name}"] = $setting->getValue();
					}
				}
			}
			return $data;
		}
	}
}
// End of file: Core.class.php
// Location: library/configuration/handler/Core.class.php