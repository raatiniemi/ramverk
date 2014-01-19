<?php
namespace Me\Raatiniemi\Ramverk\Data\Dom\Utility
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Handle value retrieval for DOM nodes/elements.
	 *
	 * @package Ramverk
	 * @subpackage Data
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	trait Value
	{
		/**
		 * Retrieve the value from the DOM node.
		 * @return mixed Value from the DOM node.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getValue()
		{
			return isset($this->nodeValue) ? $this->handleTypecast($this->nodeValue) : NULL;
		}

		/**
		 * Retrieve the attribute value.
		 * @param string $name Name of the attribute.
		 * @param mixed $default Default value.
		 * @return mixed Value of the attribute or default value.
		 */
		public function getAttribute($name, $default=NULL)
		{
			$value = $default;

			if($this->hasAttribute($name)) {
				$value = parent::getAttribute($name);
			}

			return $this->handleTypecast($value);
		}

		/**
		 * Handles typecasting of values.
		 * @param string $value Value to typecast.
		 * @return mixed Typecast value.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function handleTypecast($value)
		{
			if(preg_match('/^(true|false)$/i', $value)) {
				$value = strtolower($value) === 'true' ? TRUE : FALSE;
			} elseif(preg_match('/^([0-9]+)$/', $value)) {
				$value = (integer)$value;
			} elseif(preg_match('/^([0-9\.]+)$/', $value)) {
				$value = (double)$value;
			}

			return $value;
		}
	}
}
// End of file: Value.trait.php
// Location: library/data/dom/utility/Value.trait.php