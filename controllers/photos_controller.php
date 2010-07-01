<?php
class PhotosController extends AppController
{

	var $name = 'Photos';
	var $helpers = array('Xmlbuilder','Jsonbuilder');

	function index() 
	{
		
		// URL Beispiele
		// http://localhost/cakephp/photos?id=2
		// http://localhost/cakephp/photos?tags=winter,eis&limit=10
		
		$allowedQryParams = array("id"=>"id");
		$allowedCtrlParams = array(
			"apikey" => "",
			"tags" => "",
			"geoframe" => "",
			"sortby" => array("title"=>"Photo.title","created"=>"Photo.created","author"=>"Photo.user_name","rating"=>"COUNT(Rating.value)","views"=>"Photo.views"),
			"searchterm" => "",
			"offset" => 0,
			"limit" => 0,
			"format" => array("xml","json","img","smallimg","thumbnail","tinyimg")
			);
		
		$invalidParams = false;
		$model = "";
		$urlParams = array();
		
		// kind of default settings spec
		$parsedParams=array();
		$parsedParams["format"]="xml";
		$parsedParams["urlparams"]=array();
		$parsedParams["offset"]=$allowedCtrlParams["offset"];
		$parsedParams["limit"]=$allowedCtrlParams["limit"];
		$parsedParams["tags"]=array();
		$parsedParams["searchterm"]=array();
		$parsedParams["sortby"]="";
		
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
							case "searchterm":
							{
								$val = preg_replace('/[^a-zA-Z0-9öÖüÜäÄß_% ]/','',$val);
								//$val = str_replace(' ','%', $val);
								$parsedParams["searchterm"] = array('OR' => array(
									array($model.".title LIKE '%".$val."%'"),
									array($model.".description LIKE '%".$val."%'")
									)
								);
								break;
							}
							case "tags":
							{	
								$val = preg_replace('/[^a-zA-Z0-9öÖüÜäÄß_,]/','',$val);
								$tags = preg_split('/,/',$val,-1,PREG_SPLIT_NO_EMPTY);
								if($tags) {
									$tgs = array();
									//$s = "";
									foreach ($tags as $tag) 
									{
										$tgs = array_merge($tgs,array(array("Tag.tag_name LIKE '".$tag."'")));
										//$s .= "'".$tag."',";
									}
									$parsedParams["tags"] = array('OR' => $tgs);
									//$parsedParams["tags"] = array("`Tag`.`tag_name` IN (".substr($s,0,strlen($s)-1).")");
								}
								break;
							}
							case "geoframe":
							{	
								//TODO
								break;
							}
							case "sortby":
							{
								if (array_key_exists($val, $allowedCtrlParams["sortby"]))
									$parsedParams["sortby"] = $allowedCtrlParams["sortby"][$val];
								else 
									$invalidParams = true;	
								break;
							}
							case "offset":
							{	
								$parsedParams["offset"]= (int)preg_replace('/[^0-9]/','',$val);
								break;
							}
							case "limit":
							{
								$parsedParams["limit"] = (int)preg_replace('/[^0-9]/','',$val);
								break;
							}
							case "format":
							{
								if (in_array($val, $allowedCtrlParams["format"]))
									$parsedParams["format"] = $val;
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
			//
			// ____stuff for db request starts HERE_____
			//
		  {
			// creating $parsedParams["urlparams"] for conditions/where-clause
			if ($urlParams) 
			{
				$parsedParams["urlparams"] = array('AND' => $urlParams);
			}
			
			// join photos <> tags & ratings
			$joins = array(
				array('table' => 'tags',
					'alias' => 'Tag',
					'type' => 'LEFT',
					'conditions' => array(
						'Photo.id = Tag.photo_id',
					)
				),
				array('table' => 'ratings',
					'alias' => 'Rating',
					'type' => 'LEFT',
					'conditions' => array(
						'Photo.id = Rating.photo_id',
					)
				)
			);
			
			// for offset : NEW_limit = limit + offset
			if ($parsedParams["offset"] > 0 && $parsedParams["limit"] > 0) $parsedParams["limit"] += $parsedParams["offset"];
				
			
			// conditions/where-clause available OR null
			if ($parsedParams["urlparams"] || $parsedParams["searchterm"] || $parsedParams["tags"])
				$conditions = $parsedParams["searchterm"]; /*array('AND' => array(
					$parsedParams["urlparams"],
					$parsedParams["searchterm"]//,
					$parsedParams["tags"]
					));*/
			else $conditions = null; 
			
			// db request
			$results = $this->Photo->find('all', array(
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => $parsedParams["sortby"],
				'group' => array($model.'.id'),
				'limit' => $parsedParams["limit"]
				//,'page' => 2
			));
			
			// offset : delete from results position 0 to offset 
			if ($parsedParams["offset"] > 0)
			{
				$offset = (int)$parsedParams["offset"];
				foreach ($results as $key => $val)
				{
					if ($offset>0) 
					{
						$offset--;
						unset($results[$key]);
					}
				}
			}
		  }
			//
			// ____stuff for db request ends HERE_____
			//
			
			// format : check image types & db result size = 1
			if (in_array($parsedParams["format"],array("img","smallimg","thumbnail","tinyimg")))
			{
				if(sizeof($results) == 1)
				{
					$results['imgtype'] = $parsedParams["format"]; // add param "imgtype" for view switch
				}
				else
				{
					$results = null;
				}
			}
				
			// setting vars for views
			$this->set("results",$results);
			
			switch ($parsedParams["format"]) 
			{
				case "img":
					$this->render('\\'.$model.'s\img\index','\img\default',null);
					break;
				case "smallimg":
					$this->render('\\'.$model.'s\img\index','\img\default',null);
					break;
				case "thumbnail":
					$this->render('\\'.$model.'s\img\index','\img\default',null);
					break;
				case "tinyimg":
					$this->render('\\'.$model.'s\img\index','\img\default',null);
					break;	
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
		echo "TEST add";
		/*
		if (!empty($this->data)) {
			$this->Photo->create();
			if ($this->Photo->save($this->data)) {
				$this->Session->setFlash(__('The photo has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The photo could not be saved. Please, try again.', true));
			}
		}
		$users = $this->Photo->User->find('list');
		$this->set(compact('users'));
		*/
	}

	function edit() {
		echo "TEST edit";
		/*
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid photo', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Photo->save($this->data)) {
				$this->Session->setFlash(__('The photo has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The photo could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Photo->read(null, $id);
		}
		$users = $this->Photo->User->find('list');
		$this->set(compact('users'));
		*/
	}

	function delete() {
		echo "TEST del";
		/*
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for photo', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Photo->delete($id)) {
			$this->Session->setFlash(__('Photo deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Photo was not deleted', true));
		$this->redirect(array('action' => 'index'));
		*/
	}
}
?>