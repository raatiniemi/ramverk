<?php
namespace Me\Raatiniemi\Ramverk\Request
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Utility\Http;

	class Web extends Ramverk\Request
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Http\Header;

		protected $_requestUri;

		protected $_requestMethod;

		protected $_requestData;

		protected $_requestRawData;

		public function __construct()
		{
			// TODO: Handle custom request URI indexes.
			// Retrieve the value for the specified request URI index.
			$this->_requestUri = isset($_GET['uri']) ? trim($_GET['uri'], '/') : NULL;

			// Retrieve the method used for the request.
			$this->_requestMethod = 'read';
			if(isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
				$this->_requestMethod = 'write';
			}
			unset($_GET['uri'], $_SERVER['REQUEST_METHOD']);

			$this->_requestData = NULL;
			$this->_requestRawData = array();

			// Process the raw request data, save what is neccessary and discard
			// everything else. Handles content type specific data.
			$this->processRawData();

			// Reset the available input sources since we have stored the raw data.
			// The raw data will be validated on request.
			$_GET = $_POST = $_REQUEST = array();
			$_SERVER['REQUEST_URI'] = $_SERVER['QUERY_STRING'] = NULL;
		}

		protected function processRawData()
		{
			$data = array();
			$data = array_merge($data, !empty($_GET) ? $_GET : array());

			if($this->getRequestMethod() === 'Write') {
				$data = array_merge($data, !empty($_POST) ? $_POST : array());

				// Different content types send data on different channels, and
				// needs to be handled separately.
				$type = $this->getHeader('content-type');
				if(isset($type)) {
					switch(strtolower($type)) {
						case 'application/json':
							$input = file_get_contents('php://input');
							$input = json_decode($input, TRUE);
							if(!empty($input)) {
								$data = array_merge($data, $input);
							}
							break;
					}
				}
			}

			$this->_requestRawData = $data;
		}

		public function getRequestUri()
		{
			return $this->_requestUri;
		}

		public function getRequestMethod()
		{
			return ucfirst($this->_requestMethod);
		}

		public function getRequestData()
		{
			if($this->_requestData === NULL) {
				// TODO: Validate the request data against the validate configuration.
			}
			return $this->_requestData;
		}

		public function getRequestRawData()
		{
			return $this->_requestRawData;
		}
	}
}
// End of file: Web.class.php
// Location: library/request/web/Web.class.php