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
class File extends \SplFileObject
{
    /**
     * Read the contents of the file.
     * @throws Me\Raatiniemi\Ramverk\Exception If file is not readable.
     * @throws Me\Raatiniemi\Ramverk\Exception If unable to read row from file.
     * @return string Contents of the file.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function read()
    {
        // Check that the file is readable.
        if (!$this->isReadable()) {
            // TODO: Write exception message.
            throw new Ramverk\Exception();
        }

        // Rewind the file pointer to the begining of the file.
        $this->rewind();

        // Iterate through the rows of the file until eof is reached.
        $rows = array();
        while (!$this->eof()) {
            // Retrieve the row from the file. If the row failed to read
            // the `fgets`-method will return false. The `fgets`-method
            // internally increment the row for the next iteration.
            $row = $this->fgets();
            if ($row === false) {
                // TODO: Write exception message.
                throw new Ramverk\Exception();
            }

            $rows[] = $row;
        }
        // Implode the retrieved rows with the cross-plattform linebreak.
        return implode(PHP_EOL, $rows);
    }

    /**
     * Write data to file.
     * @param string $data Data to write to file.
     * @throws Me\Raatiniemi\Ramverk\Exception If file is not writeable.
     * @throws Me\Raatiniemi\Ramverk\Exception If write to file fails.
     * @return int Number of bytes written to the file.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function write($data)
    {
        // Check that the file is writable.
        if (!$this->isWritable()) {
            // TODO: Write exception message.
            throw new Ramverk\Exception();
        }

        if (($bytes = $this->fwrite($data)) === null) {
            // TODO: Write exception message.
            throw new Ramverk\Exception();
        }

        return $bytes;
    }
}
// End of file: File.php
// Location: library/utility/File.php
