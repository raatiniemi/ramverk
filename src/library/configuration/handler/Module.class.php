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
		 * @throws Me\Raatiniemi\Ramverk\Exception If no configuration group have been defined.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document)
		{
			// Retrieve the configuration group from the document. Every module
			// configuration document must have one and only one configuration
			// group, otherwise it's an invalid document.
			$group = $document->getElementsByTagName('configuration');
			if(empty($group) || $group->length <> 1) {
				// TODO: Write exception message.
				// TODO: Better specify the exception object.
				throw new Ramverk\Exception('');
			}

			$data = array();
			foreach($group->item(0)->getElementsByTagName('setting') as $item) {
				// Every setting item must have the name attribute.
				if(!$item->hasAttribute('name')) {
					// TODO: Write exception message.
					// TODO: Better specify the exception object.
					throw new Ramverk\Exception('');
				}

				// Retrieve the setting name and value.
				$name = $item->getAttribute('name');
				$data["module.{$name}"] = $this->expandDirectives($item->getValue());
			}
			return $data;
		}
	}
}
// End of file: Module.class.php
// Location: library/configuration/handler/Module.class.php