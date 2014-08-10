<?php
namespace Me\Raatiniemi\Ramverk\Utility {
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
	class Filesystem {
		public function mkdir($pathname, $mode = 0777, $recursive = false) {
			return mkdir($pathname, $mode, $recursive);
		}
	}
}
// End of file: Filesystem.class.php
// Location: library/utility/Filesystem.class.php