<?php
namespace Me\Raatiniemi\Ramverk\Configuration;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;

/**
 * Utilities for working with the configuration container.
 *
 * @package Ramverk
 * @subpackage Configuration
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Utility
{
    /**
     * Expands configuration directives.
     *
     * Directive names between precentage signs, e.g. %application.name% will be
     * replaced with the actual value for the directive 'application.name'.
     *
     * Support multiple directives within the value.
     *
     * @param Me\Raatiniemi\Ramverk\Configuration $config Configuration container.
     * @param string $value String with configuration directives to expand.
     * @throws InvalidArgumentException If the value argument is not a string.
     * @return string String with configuration directives expanded.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public static function expand(Ramverk\Configuration $config, $value)
    {
        // Verify that the value with configuration directives actually is a string.
        //
        // The default value from the `get`-method from the configuration container
        // is null, so we have to allow null values.
        if (!is_string($value) && !is_null($value)) {
            throw new \InvalidArgumentException(sprintf(
                'The value with configuration directives is of type "%s" and not a string.',
                gettype($value)
            ));
        }

        // Only attempt to expand the directives if the value is not empty.
        if (!empty($value)) {
            // Continue looping until there is no more directives to expand.
            do {
                $oldValue = $value;

                // Attempt to replace the reference of the configuration
                // directive with the actual value.
                $value = preg_replace_callback(
                    '/\%([a-z0-9\.]+)\%/i',
                    function ($matches) use ($config) {
                        // Attempt to retrieve the value for the configuration
                        // directive, with the original value as fallback.
                        return $config->get($matches[1], $matches[0]);
                    },
                    $value
                );
            } while ($oldValue != $value);
        }
        return $value;
    }
}
// End of file: Utility.php
// Location: library/configuration/Utility.php
