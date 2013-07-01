<?php
namespace Net\TheDeveloperBlog\Ramverk\Configuration\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk\Configuration;

	/**
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Module implements IHandler
	{
		/**
		 * Execute the configuration handler.
		 * @param DOMDocument $document XML document with configuration data.
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Container $config Configuration container.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function execute(\DOMDocument $document, Configuration\Container $config)
		{
			$configuration = array();
			foreach($document->getElementsByTagName('settings') as $module) {
				$configuration['enabled'] = $module->hasAttribute('enabled') ? $module->getAttribute('enabled') : TRUE;

				foreach($module->getElementsByTagName('setting') as $setting) {
					if(!$setting->hasAttribute('name')) {
						// TODO: Better specify the Exception-object.
						throw new Ramverk\Exception(sprintf(
							'Module item is missing the name-attribute in '.
							'configuration file "%s".',
							$document->documentURI
						));
					}

					$name = $setting->getAttribute('name');
					$configuration[$name] = $setting->nodeValue;
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
// Location: library/config/handler/Module.class.php