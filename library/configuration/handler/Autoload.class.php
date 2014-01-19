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
	class Autoload extends Configuration\Handler
	{
		/**
		 * Execute the configuration handler.
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
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