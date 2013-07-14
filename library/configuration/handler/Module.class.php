<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Configuration\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;
	use Net\TheDeveloperBlog\Ramverk\Configuration;
	use Net\TheDeveloperBlog\Ramverk\Data\Dom;

	/**
	 * Handler for autoload configuration.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Module extends Configuration\Handler
	{
		/**
		 * Execute the configuration handler.
		 * @param Net\TheDeveloperBlog\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
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