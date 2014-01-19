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
	 * Handler for autoload configuration.
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
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document)
		{
			$configuration = array();
			foreach($document->getElementsByTagName('settings') as $module) {
				$configuration['enabled'] = $module->getAttribute('enabled', TRUE);

				foreach($module->getElementsByTagName('setting') as $setting) {
					if(!$setting->hasAttribute('name')) {
						// TODO: Better specify the Exception-object.
						throw new Ramverk\Exception(sprintf(
							'Module item is missing the name-attribute in '.
							'configuration file "%s".',
							$document->documentURI
						));
					}

					// Prefix every item from the module configuration with "module.".
					$name = "module.{$setting->getAttribute('name')}";
					$configuration[$name] = $setting->getValue();
				}

				// Since we only want one configuration per module we can
				// break free from the foreach loop.
				break;
			}
			return $configuration;
		}
	}
}
// End of file: Module.class.php
// Location: library/configuration/handler/Module.class.php