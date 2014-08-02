<?php
namespace Me\Raatiniemi\Ramverk\Request
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Core;
	use Me\Raatiniemi\Ramverk\Request\Web;

	/**
	 * Handles incoming requests for the web context.
	 *
	 * @package Ramverk
	 * @subpackage Request
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2014, Authors
	 */
	class Web extends Ramverk\Request
	{
		/**
		 * Stores the request uri.
		 * @var string
		 */
		private $uri;

		/**
		 * Initialize the web request object.
		 * @param Me\Raatiniemi\Ramverk\Core\Context $ct Application context.
		 * @param Me\Raatiniemi\Ramverk\Request\Web\Data $rd Request data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct(Core\Context $ct, Web\Data $rd)
		{
			parent::__construct($ct, $rd);
		}

		/**
		 * Set the request URI.
		 * @param string $uri Request URI.
		 * @return string Request URI.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function setUri($uri)
		{
			return $this->uri = $uri;
		}

		/**
		 * Retrieve the request URI.
		 * @return string Request URI.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getUri()
		{
			return $this->uri;
		}

		/**
		 * Processes the incoming raw data and populate the request data container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function processRawData()
		{
			// Retrieve the value for the specified request URI index.
			$index = $this->getConfig()->get('core.uri_index', 'uri');
			$uri = isset($_GET[$index]) ? trim($_GET[$index], '/') : NULL;
			$this->setUri($uri);

			// Retrieve the method used for the request.
			$method = Ramverk\Request::Read;
			if(isset($_SERVER['REQUEST_METHOD'])) {
				// Check which of the request methods are used.
				// TODO: Add support for HEAD and PUT.
				switch(strtolower($_SERVER['REQUEST_METHOD'])) {
					case 'post':
						$method = Ramverk\Request::Write;
						break;
					case 'get':
					default:
						$method = Ramverk\Request::Read;
						break;
				}
			}
			$this->setMethod($method);

			// Remove the URI index and request method, we are done with these.
			unset($_GET[$index], $_SERVER['REQUEST_METHOD']);

			// Begin assembling the request data.
			$data = array();
			$data = array_merge($data, !empty($_GET) ? $_GET : array());

			// If the request method is write, i.e. the user is most likley
			// sending in data with the request.
			if($this->getMethod() === Ramverk\Request::Write) {
				$data = array_merge($data, !empty($_POST) ? $_POST : array());

				// Different content types send data on different channels, and
				// needs to be handled separately.
				//
				// The content type specific request data have to be parsed last,
				// otherwise the data might get overridden by GET/POST data.
				$type = $this->getData()->getHeader('content-type');
				if(isset($type)) {
					// Check if there's any data available via the input channel.
					$input = file_get_contents('php://input');

					// Since the content type header can be both uppcase and lowercase
					// we have to convert it to lowercase before comparing it.
					if(!empty($input)) {
						switch(strtolower($type)) {
							case 'application/json':
								// Attempt to parse the incoming JSON data, if
								// any is available we have to merge it with
								// the rest of the request data.
								$input = json_decode($input, TRUE);
								if(!empty($input)) {
									$data = array_merge($data, $input);
								} else {
									// TODO: Invalid JSON data recieved, handle it.
								}
							break;
						}
					}
				}
			}

			// Set the raw request data.
			$this->getData()->setRaw($data);

			// Reset the available input sources since we have stored the raw data.
			// The raw data will be validated on request.
			$_GET = $_POST = $_REQUEST = array();
			$_SERVER['REQUEST_URI'] = $_SERVER['QUERY_STRING'] = NULL;
		}
	}
}
// End of file: Web.class.php
// Location: library/request/web/Web.class.php