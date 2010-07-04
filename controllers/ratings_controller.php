<?php
class RatingsController extends AppController {

	var $name = 'Ratings';

	var $helpers = array('Xmlbuilder','Jsonbuilder');	
	function index() 
	{
		// URL Beispiele
		// http://localhost/cakephp/ratings?id=2
		// http://localhost/cakephp/ratings?photoid=1
		// http://localhost/cakephp/ratings?id=1&photoid=1	
		$allowedQryParams = array(
			"id"=>"id",
			"ratingid"=>"photo_id"
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
					$val = preg_replace('/[^a-zA-Z0-9�������_]/','',$val);
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
			$results = $this->Rating->find('all', array(
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
		if (!empty($this->data)) {
			$this->Rating->create();
			if ($this->Rating->save($this->data)) {
				$this->Session->setFlash(__('The rating has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The rating could not be saved. Please, try again.', true));
			}
		}
		$photos = $this->Rating->Photo->find('list');
		$users = $this->Rating->User->find('list');
		$this->set(compact('photos', 'users'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid rating', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Rating->save($this->data)) {
				$this->Session->setFlash(__('The rating has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The rating could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Rating->read(null, $id);
		}
		$photos = $this->Rating->Photo->find('list');
		$users = $this->Rating->User->find('list');
		$this->set(compact('photos', 'users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for rating', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Rating->delete($id)) {
			$this->Session->setFlash(__('Rating deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Rating was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>