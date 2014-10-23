<?php
namespace Me\Raatiniemi\Ramverk\Utility;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;

/**
 * Trait for working with the filesystem.
 *
 * @package Ramverk
 * @subpackage Utility
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
trait Filesystem
{
    /**
     * Check if the path is a directory.
     * @param string $filename Path to check.
     * @return boolean True if path is a directory, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function isDirectory($filename)
    {
        return is_dir($filename);
    }

    /**
     * Check if the path is a file.
     * @param string $filename Path to check.
     * @return boolean True if path is a file, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function isFile($filename)
    {
        return is_file($filename);
    }

    /**
     * Check if the path is readable.
     * @param string $path Path to check.
     * @return boolean True if path is readable, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * Check if the path is writable.
     * @param string $path Path to check.
     * @return boolean True if path is writable, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * Attempt to create directory at given path.
     * @param string $pathname Directory path to create.
     * @param integer $mode Permissions for the directory.
     * @param boolean $recursive True for recursion, otherwise single level.
     * @throws Me\Raatiniemi\Ramverk\Exception If path exists but is not a directory.
     * @throws Me\Raatiniemi\Ramverk\Exception If unable to create directory.
     * @return boolean True if directory was created or already exists, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function makeDirectory($pathname, $mode = 0777, $recursive = false)
    {
        $returnValue = true;

        // Check if the directory path already exists.
        if ($this->isReadable($pathname)) {
            // Since the path already exists, we have to verify that it's
            // actually a directory and not a file.
            if (!$this->isDirectory($pathname)) {
                // TODO: Write exception message.
                // TODO: Better specify the exception object.
                throw new Ramverk\Exception('');
            }

            // TODO: Check permissions, and attempt to fix if incorrect.
        } else {
            // Attempt to create the directory.
            if (!($returnValue = mkdir($pathname, $mode, $recursive))) {
                // TODO: Write exception message.
                // TODO: Better specify the exception object.
                throw new Ramverk\Exception('');
            }
        }

        return $returnValue;
    }
}
// End of file: Filesystem.php
// Location: library/utility/Filesystem.php
