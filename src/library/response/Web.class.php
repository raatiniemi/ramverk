<?php
namespace Me\Raatiniemi\Ramverk\Response
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Utility\Http;

	class Web extends Ramverk\Response
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Http\Header;

		protected $_accepts;

		protected function sortByQuality(array $data=array())
		{
			$sorted = array();
			foreach($data as $value) {
				if(strstr($value, ';q=')) {
					// Have to use manual assignment of the array values, when using the
					// list function Zend Guard will exit with segmentation fault.
					//
					// list($type, $quality) = explode(';q=', $value);
					$value = explode(';q=', $value);
					$type = $value[0];
					$quality = $value[1];
				} else {
					$type = $value;
					$quality = '1.0';
				}
				$sorted[$quality][] = $type;
			}
			unset($data, $value);

			// Sort the entries by their quality, e.g.: 1.0 -> 0.0
			ksort($sorted, SORT_NUMERIC);
			$sorted = array_reverse($sorted);

			$data = array();
			foreach($sorted as $quality) {
				foreach($quality as $value) {
					$data[] = $value;
				}
			}
			unset($sorted, $quality, $value);

			return $data;
		}

		public function getAccept()
		{
			if($this->_accepts === NULL) {
				// Check if the accept header have been supplied, otherwise fallback to "text/html".
				// TODO: Allow for different fallback depending on output and context configuration.
				$accept = $this->getHeader('accept', 'text/html');
				$accepts = explode(',', $accept);
				unset($accept);

				$this->_accepts = $this->sortByQuality($accepts);
			}
			return $this->_accepts;
		}
	}
}
// End of file: Web.class.php
// Location: library/response/Web.class.php