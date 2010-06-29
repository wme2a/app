<?php
class CommentsController extends AppController {

    var $name = "Comments";
	var $components = array('RequestHandler');
	var $helpers = array('Xmlbuilder','Jsonbuilder');
	
	function index() {
	
		// URL Beispiele
		// http://localhost/cakephp/comments?id=2
		// http://localhost/cakephp/comments?photoid=1
		// http://localhost/cakephp/comments?id=1&photoid=1
		
		$allowedQryParams = array(
			"id"=>"id",
			"photoid"=>"photo_id"
			);
		$allowedCtrlParams = array(
			"apikey" => "",
			"format" => array("xml","json")
			);
			
		$invalidParams=false;
		$model = " ";
		$urlParams = array();
		
		$parsedParams["format"]="html";
		$parsedParams["urlparams"]=array();
		
		foreach ($this->params['url'] as $key => $val) {
			if ($key=="url") {
				$model=ucfirst(preg_replace('/[^a-z]/','',strtolower($val))); // first letter uppercase
				$model=substr($model,0,strlen($model)-1);
			}
			else {
				$key = strtolower($key);
				
				if (array_key_exists($key, $allowedQryParams))
				{
					$val = preg_replace('/[^a-zA-Z0-9öÖüÜäÄß_]/','',$val);
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
									if (array_key_exists('ext', $this->params['url'])) $this->params['url']['ext'] = $parsedParams["format"];
								}
								else 
									$invalidParams = true;	
								break;
							}
								
							case "apikey":
							{ 	//TODO
								//echo $key . " = " . $val . "<br />";
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
				//echo var_dump($parsedParams["urlparams"])."<br />";
			}
			
			// conditions/where-clause available OR null
			if ($parsedParams["urlparams"])
				$conditions = array('AND' => array(
					$parsedParams["urlparams"]
					));
			else $conditions = null; 
				
			// db request
			$result = $this->Comment->find('all', array(
				'conditions' => $conditions,
			));
			
			switch ($parsedParams["format"]) 
			{
				case "html": 
				{
					echo "<br />STATUS:<b><font color='green'>&nbsp;OK</font><b><br /><br />";
					$this->set("results",$result);
					break;
				}
				case "json": 
					//TODO
					$this->set("results",$result);
					$this->render('\\'.$model.'s\json\index','\json\default',null);
					break;
				default: //"xml"
					$this->set("results",$result);
					$this->render('\\'.$model.'s\xml\index','\xml\default',null);
					break;
					//$this->layout="xml";
					//$this->redirect("/comments/index");
					//$this->autoRender = false;
					break;
			}
		}
		else
		{
			echo "<br />STATUS:<b><font color='red'>&nbsp;INVALID-PARAMS !!</font><b><br /><br />";
			$this->set("results",null);
		}
	}
}
?>