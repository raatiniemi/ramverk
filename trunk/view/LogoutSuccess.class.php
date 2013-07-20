<?php
	class LogoutSuccess extends View
	{
		public function executeHtml()
		{
			return $this->setupHtml(array('hej'));
		}

		public function executeJson()
		{
			return json_encode(array(
				'status' => 'success',
				'method' => __METHOD__
			));
		}
	}