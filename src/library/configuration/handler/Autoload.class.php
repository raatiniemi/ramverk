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
		 * @throws Me\Raatiniemi\Ramverk\Exception If no autoload groups are found.
		 * @throws Me\Raatiniemi\Ramverk\Exception If an autoload item is missing the name-attribute.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document)
		{
			// Retrieve the autoload groups from the document. Every document must
			// have atleast one autoload group, otherwise it's an invalid document.
			$groups = $document->getElementsByTagName('autoloads');
			if(empty($groups) || $groups->length === 0) {
				// TODO: Write exception message.
				// TODO: Better specify the exception object.
				throw new Ramverk\Exception('');
			}

			$data = array();
			foreach($groups as $group) {
				// If the autoload group has defined a namespace, then this
				// namespace will prefix every class within the group.
				$namespace = $group->hasAttribute('namespace') ? "{$group->getAttribute('namespace')}\\" : NULL;

				// Retrieve the autoload items from the group.
				$items = $group->getElementsByTagName('autoload');
				foreach($items as $item) {
					// Every item must have the name of the class defined.
					if(!$item->hasAttribute('name')) {
						// TODO: Write exception message.
						// TODO: Better specify the exception object.
						throw new Ramverk\Exception('');
					}

					// Prepend the group namespace (if any) to the class name,
					// and expand the class path directives.
					$name = "{$namespace}{$item->getAttribute('name')}";
					$data[$name] = $this->expandDirectives($item->getValue());
				}
			}
			// TODO: Should we throw an exception if $data is empty?
			return $data;
		}
	}
}
// End of file: Autoload.class.php
// Location: library/configuration/handler/Autoload.class.php