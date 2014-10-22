<?php
namespace Me\Raatiniemi\Ramverk\Utility;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;

/**
 * @package Ramverk
 * @subpackage Utility
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
trait Filesystem
{
    public function isDirectory($filename)
    {
        return is_dir($filename);
    }

    public function isFile($filename)
    {
        return is_file($filename);
    }

    public function isReadable($path)
    {
        return is_readable($path);
    }

    public function isWritable($path)
    {
        return is_writable($path);
    }

    public function makeDirectory($pathname, $mode = 0777, $recursive = false)
    {
        $returnValue = true;

        // Check if the directory path already exists.
        if ($this->isReadable($pathname)) {
            // Since the path already exists, we have to verify that it's
            // actually a directory and not a file.
            if (!$this->isDirectory($pathname)) {
                // TODO: Write exception message.
                throw new Ramverk\Exception();
            }

            // TODO: Check permissions, and attempt to fix if incorrect.
        } else {
            // Attempt to create the directory.
            if (!($returnValue = mkdir($pathname, $mode, $recursive))) {
                // TODO: Write exception message.
                throw new Ramverk\Exception();
            }
        }

        return $returnValue;
    }
}
// End of file: Filesystem.php
// Location: library/utility/Filesystem.php
