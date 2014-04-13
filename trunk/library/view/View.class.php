<?php
namespace Me\Raatiniemi\Ramverk\Trunk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;

	class View extends Ramverk\View
	{
		public function execute()
		{
			throw new Exception('Method is not available');
		}

		public function setupHtml(array $data=array(), $template=NULL)
		{
			if($template === NULL) {
				$class = get_class($this);
				$template = substr($class, (strrpos($class, '\\') + 1));
			}
			$template = realpath("template/{$template}.php");

			if(file_exists($template)) {
				ob_start();
				require $template;
				$inner = ob_get_contents();
				ob_end_clean();

				ob_start();
				require 'template/Master.php';
				$html = ob_get_contents();
				ob_end_clean();

				return $html;
			} else {
				throw new \Exception("Template not found");
			}
		}
	}
}