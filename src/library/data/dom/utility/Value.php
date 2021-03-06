<?php
namespace Me\Raatiniemi\Ramverk\Data\Dom\Utility;

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
        return $this->hasValue() ? $this->handleTypecast($this->nodeValue) : null;
    }

    /**
     * Check whether the DOM node has a value.
     * @return boolean True if the nodeValue is set, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function hasValue()
    {
        // Check that the nodeValue have been set, and that it's not an
        // empty string. Anything else is acceptable.
        //
        // Unable to use the empty-function since that would evaulate zero
        // values to be empty and that's not necessarily what we'd want.
        return isset($this->nodeValue) && $this->nodeValue !== '';
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
                $value = (integer) $value;
            } elseif (preg_match('/^([0-9\.]+)$/', $value)) {
                $value = (double) $value;
            }
        }

        return $value;
    }
}
// End of file: Value.php
// Location: library/data/dom/utility/Value.php
