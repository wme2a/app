<?php
class CommentsController extends AppController
{
    var $name = 'Comments';
	var $helpers = array('Xmlbuilder','Jsonbuilder');
	
	function index() 
	{
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
			$results = $this->Comment->find('all', array(
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
		else $this->set("results",null);
	}
	
	function add() 
	{
		$xml_test='<?xml version="1.0" encoding="UTF-8"?><pp:com_ments xmlns:pp="http://www-mmt.inf.tu-dresden.de/Lehre/Sommersemester_10/Vo_WME/Uebung/material/photonpainter"><pp:comment id="4" photo_id="2" user_id="2" title="Sparkurs">Dass die Uni so sehr sparen muss...</pp:comment></pp:com_ments>';
		
		$model = ucfirst(substr($this->params["controller"],0,-1));
		
		App::import('Helper', 'Xmlbuilder');
		$x = new XmlbuilderHelper();
		if ($x->validate($xml_test)) // validate file stream source
		{
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->loadXML($xml_test);
			
			$xpath = new DOMXPath($doc);
			$tag = $xpath->query('//pp:comments/pp:comment')->item(0); // tag to write to db
			
			if ($tag) 
			{
				$update = array();
				foreach ($tag->attributes as $k => $v) 
				{
					$update[$model][$k] = $v->textContent; 
				}
				$update[$model]["comment_text"] = $tag->nodeValue; 
				$update[$model]["id"] = null; // to force new id in db with create()

				//$this->Comment->create(); // create(): generates new id, if isn't set or is null
				//$this->Comment->save($update); // save to db
				
				header("HTTP/1.0 201 Created");
				return true;
			}
		} 
		header("HTTP/1.0 412 Precondition Failed");
		echo "";
		return false;
	}

	function delete() 
	{
		$id = array_key_exists("id", $this->params['url']) ? intval($this->params['url']['id']) : null;
		
		if ($id && $this->Comment->delete($id, true)) // delete cascaded
		{	
			return true;
		}
		header("HTTP/1.0 404 Not Found");
		echo "";
		return false;
	}
}
?>