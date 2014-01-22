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
	 * Handler for routing configuration.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Routing extends Configuration\Handler
	{
		/**
		 * Execute the configuration handler
		 * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function execute(Dom\Document $document)
		{
			// Attempt to retrieve the route groups from the document.
			$data = array();
			$groups = $document->getElementsByTagName('routes');
			if(!empty($groups)) {
				foreach($groups as $group) {
					// Retrieve the route items from the group.
					$items = $group->getElementsByTagName('route');
					foreach($items as $item) {
						$route = array();

						// Verify that the route have all of the required attributes.
						foreach(array('name', 'pattern', 'module', 'action') as $attribute) {
							if(!$item->hasAttribute($attribute)) {
								// TODO: Write exception message.
								// TODO: Better specify the exception object.
								throw new Ramverk\Exception('');
							}
							// Assign the attribute value to the route configuration.
							$route[$attribute] = $item->getAttribute($attribute);
						}

						// Add the route configuration to the route.
						$data[] = $route;
					}
				}
			}
			return $data;
		}
	}
}
// End of file: Routing.class.php
// Location: library/configuration/handler/Routing.class.php