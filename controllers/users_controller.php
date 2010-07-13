<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Xmlbuilder','Jsonbuilder');
	var $components = array('RequestHandler');
	function index() 
	{
		// URL Beispiele
		// http://localhost/cakephp/users?id=2
		// http://localhost/cakephp/users?photoid=1
		// http://localhost/cakephp/users?id=1&photoid=1	
		$allowedQryParams = array(
			"id"=>"id",
			"userid"=>"user_id"
			);
		$allowedCtrlParams = array(
			"apikey" => "",
			"format" => array("xml","json")
			);
			
		$invalidParams = false;
		$model = "";
		$urlParams = array();
		
		// kind of default settings spec
		$parsedParams=array();
		$parsedParams["format"]="xml";
		$parsedParams["urlparams"]=array();
		
		foreach ($this->params['url'] as $key => $val) {
			if ($key=="url") {
				$model=ucfirst(preg_replace('/[^a-z]/','',strtolower($val)));
				$model=substr($model,0,strlen($model)-1);
			}
			else {
				$key = strtolower($key);
				
				if (array_key_exists($key, $allowedQryParams))
				{
					$val = preg_replace('/[^a-zA-Z0-9φΦόάδΔί_]/','',$val);
					$urlParams = array_merge($urlParams,array(array($model.'.'.$allowedQryParams[$key] => $val)));
				}
				else 
				{
					if (array_key_exists($key, $allowedCtrlParams)) 
					{
						switch($key) {
							case "format":
							{
								if (in_array($val, $allowedCtrlParams["format"]))
								{
									$parsedParams["format"] = $val;
								}
								else 
									$invalidParams = true;	
								break;
							}
								
							case "apikey":
							{ 	
								// has not to be implemented
								break;
							}
						}
					}
					else 
					{
						if ($key != 'ext') $invalidParams=true; //if Routes w/ Router::parseExtensions()
					}
				}
			}
		}
		
		if (!$invalidParams)
		{
			// creating $parsedParams["urlparams"] for conditions/where-clause
			if ($urlParams) 
			{
				$parsedParams["urlparams"] = array('AND' => $urlParams);
			}
			
			// conditions/where-clause available OR null
			if ($parsedParams["urlparams"])
				$conditions = array('AND' => array(
					$parsedParams["urlparams"]
					));
			else $conditions = null; 
				
			// db request
			$results = $this->User->find('all', array(
				'conditions' => $conditions,
			));
			
			// setting vars for views
			$this->set("results",$results);
			
			switch ($parsedParams["format"]) 
			{
			
				case "json": 
					$this->render('\\'.$model.'s\json\index','\json\default',null);
					break;
				default: //"xml"
					$this->render('\\'.$model.'s\xml\index','\xml\default',null);
					break;
			}
		}
		else
		{
			$this->set("results",null);
			//$this->render('\errors\invalide_params','default',null);
		}
	}
	function add() {
		$d = $this->data;
		if (!empty($d)) {
			$this->User->create();
			if ($this->User->save($d)) {
				header("HTTP/1.0 201 Created");
					return true;
			} else {
				header("HTTP/1.0 412 Precondition Failed");
				echo "";
				return false;
			}
		}
	}
	
	function delete($id = null) {
		$id = array_key_exists("id", $this->params['url']) ? intval($this->params['url']['id']) : null;
		if ($this->User->delete($id)) {
			return true;
		}
		header("HTTP/1.0 404 Not Found");
		echo "";
		return false;
	}
}
?>