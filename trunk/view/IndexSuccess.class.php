<?php
	class IndexSuccess extends View
	{
		public function executeHtml()
		{
			return $this->setupHtml();
		}

		public function executeJson()
		{
			return json_encode(array(
				'status' => 'success',
				'method' => __METHOD__
			));
		}
	}