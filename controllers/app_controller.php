<?php

class AppController extends Controller
{
	public function beforeFilter()
	{
		if ($this->params["controller"] == "pages")
		{
			switch(substr($this->params["url"]["url"],-3)) // parse file extension
			{
				case "xsd": 
					$this->layout = '/xml/default';
					break;
				case "xml": 
					$this->layout = '/xml/default';
					break;
			}
		}
	}
} 
?>