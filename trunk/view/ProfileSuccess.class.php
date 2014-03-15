<?php
	class ProfileSuccess extends View
	{
		public function executeHtml()
		{
			return $this->setupHtml();
		}

		public function executeJson(array $params=array())
		{
			return json_encode(array(
				'status' => 'success',
				'method' => __METHOD__,
				'parameters' => $params
			));
		}
	}