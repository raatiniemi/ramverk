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
			return isset($this->nodeValue) ? $this->handleTypecast($this->nodeValue) : null;
		}

		/**
		 * Retrieve the attribute value.
		 * @param string $name Name of the attribute.
		 * @param mixed $default Default value.
		 * @return mixed Value of the attribute or default value.
		 */
		public function getAttribute($name, $default = null)
		{
			// Attempt to retrieve the value for the attribute if it's available.
			$value = $this->hasAttribute($name) ? parent::getAttribute($name) : $default;

			// Since the `getAttribute`-method always return a string we have
			// to handle the cast to the correct type manually.
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
			// Check that the value is not empty and actually is a string. Will
			// silence the warnings with preg_match and non-string subjects.
			if (!empty($value) && is_string($value)) {
				if (preg_match('/^(true|false)$/i', $value)) {
					$value = strtolower($value) === 'true' ? true : false;
				} elseif (preg_match('/^([0-9]+)$/', $value)) {
					$value = (integer)$value;
				} elseif (preg_match('/^([0-9\.]+)$/', $value)) {
					$value = (double)$value;
				}
			}
			return $value;
		}
	}
}
// End of file: Value.trait.php
// Location: library/data/dom/utility/Value.trait.php