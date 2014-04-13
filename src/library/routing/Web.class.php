<?php
namespace Me\Raatiniemi\Ramverk\Routing
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Request;

	class Web extends Ramverk\Routing
	{
		public function __construct(Request\Web $request, array $routes=array())
		{
			parent::__construct($request, $routes);
		}

		public function getActionMethod(\ReflectionClass $reflection)
		{
			$method = sprintf('execute%s', $this->_request->getRequestMethod());
			if(!$reflection->hasMethod($method)) {
				$method = 'execute';
			}
			return $method;
		}

		public function parse()
		{
			$uri = $this->_request->getRequestUri();
			foreach($this->_routes as $route) {
				$parameters = array();

				// If the routing pattern contains the { character, e.g. parameter based
				// regex is used within the pattern. We have to extract the data.
				if(strstr($route['pattern'], '{')) {
					// Build the rexex used to match arguments routes.
					// The key will be used to match the value within the argument container.
					$regex = sprintf(
						'/\{{1}%1$s%2$s\}{1}/i',
						'([a-z]+)\:', // key, e.g. id:
						'([\\a-z\+\*\(\)\?]+)' // value, e.g. (\d+)
					);

					if(preg_match_all($regex, $route['pattern'], $matches)) {
						// Only attempt to extract the route parameters if we have all of the
						// necessary data, i.e. the matched values, the keys, and the patterns.
						if(isset($matches[0], $matches[1], $matches[2])) {
							$matched = $matches[0];
							$keys = $matches[1];
							$patterns = $matches[2];

							// Iterate through each of the matched parameters.
							foreach($matched as $index => $match) {
								$parameters[] = $keys[$index];

								// Replace the parameter syntax with the actual regex for the parameter.
								// Otherwise, we'll not be able to match the route against the URI.
								$route['pattern'] = str_replace($match, "({$patterns[$index]})", $route['pattern']);
							}
						}
					}
				}

				if(preg_match("#{$route['pattern']}#i", $uri, $matches)) {
					$this->_module = $route['module'];
					$this->_action = $route['action'];

					// If the route have possible parameters we have to extract these
					// to the parameters index for the action.
					if(!empty($parameters)) {
						$data = array();
						foreach($parameters as $index => $name) {
							// The key have to be incremented with one to exclude the
							// matches within the regex. We only want the actual values.
							$key = $index + 1;
							if(array_key_exists($key, $matches)) {
								// If the value exists, assign it to the data container.
								//
								// Depending on the syntax of the regex we might include
								// the URI separators (i.e. slashes), these have to be
								// stripped from the actual value.
								$data[$name] = trim($matches[$key], '/');
							}
						}
						$this->_params = $data;
					}

					break;
				}
			}

			return array(
				'module' => $this->getModule(),
				'action' => $this->getAction(),
				'params' => $this->getParams()
			);
		}
	}
}
// End of file: Web.class.php
// Location: library/routing/Web.class.php