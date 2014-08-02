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
		 * @throws Me\Raatiniemi\Ramverk\Exception If no configuration group have been defined.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document)
		{
			$data = array();
			foreach($document->getConfigurationElements() as $configuration) {
				// Check whether the configuration have `settings` defined.
				if($configuration->has('settings')) {
					foreach($configuration->get('settings') as $settings) {
						if($settings->has('setting')) {
							foreach($settings->get('setting') as $setting) {
								// Every setting item must have the name attribute.
								if(!$setting->hasAttribute('name')) {
									// TODO: Throw exception, no name setting is not allowed.
								}

								// Retrieve the setting name and value.
								$name = $setting->getAttribute('name');
								$data["module.{$name}"] = $this->expandDirectives($setting->getValue());
							}
						}
					}
				}
			}
			return $data;
		}
	}
}
// End of file: Module.class.php
// Location: library/configuration/handler/Module.class.php