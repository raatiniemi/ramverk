<?php
namespace Me\Raatiniemi\Ramverk\Data\Dom
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * @package Ramverk
	 * @subpackage Data
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Element extends \DOMElement
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Utility\Value;

		/**
		 * Retrieve the name of the element.
		 * @return string Name of the element.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getName()
		{
			return $this->nodeName;
		}

		/**
		 * Retrieve child elements with specified name.
		 * @param string $name Name of the children to retrieve.
		 * @return DOMNodeList List with the DOM nodes.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function get($name)
		{
			return $this->getChildren($name);
		}

		/**
		 * Retrieve child elements with specified name.
		 * @param string $name Name of the children to retrieve.
		 * @return DOMNodeList List with the DOM nodes.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getChildren($name)
		{
			// Query for child elements with the specified name.
			$query = sprintf('child::*[local-name() = "%s"]', $name);
			return $this->ownerDocument->getXPath()->query($query, $this);
		}
	}
}
// End of file: Element.class.php
// Location: library/data/dom/Element.class.php