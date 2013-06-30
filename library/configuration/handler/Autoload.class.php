<?php
namespace Net\TheDeveloperBlog\Ramverk\Config\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * @package Ramverk
	 * @subpackage Config
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Autoload implements IHandler
	{
		/**
		 * Execute the configuration handler.
		 * @param DOMDocument $document XML document with configuration data.
		 * @param Net\TheDeveloperBlog\Ramverk\Config $config Configuration container.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function execute(\DOMDocument $document, Ramverk\Config $config)
		{
			$autoloads = array();
			foreach($document->getElementsByTagName('autoload') as $autoload) {
				if(!$autoload->hasAttribute('name')) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Autoload item is missing the name-attribute in '.
						'configuration file "%s".',
						$document->documentURI
					));
				}

				$name = $autoload->getAttribute('name');
				$autoloads[$name] = $config->expandDirectives($autoload->nodeValue);
			}
			return $autoloads;
		}
	}
}
// End of file: Autoload.class.php
// Location: library/config/handler/Autoload.class.php