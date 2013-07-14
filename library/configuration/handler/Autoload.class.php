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
	class Autoload extends Configuration\Handler
	{
		/**
		 * Execute the configuration handler.
		 * @param Net\TheDeveloperBlog\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function execute(Dom\Document $document)
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
				$autoloads[$name] = $this->expandDirectives($autoload->getValue());
			}
			return $autoloads;
		}
	}
}
// End of file: Autoload.class.php
// Location: library/configuration/handler/Autoload.class.php