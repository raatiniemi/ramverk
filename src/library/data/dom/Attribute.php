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
	class Attribute extends \DOMAttr
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Utility\Value;

		/**
		 * Retrieve the value for the attribute.
		 * @return string Attribute value.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __toString()
		{
			return $this->getValue();
		}
	}
}
// End of file: Attribute.class.php
// Location: library/data/dom/Attribute.class.php