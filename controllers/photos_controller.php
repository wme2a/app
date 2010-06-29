<?php
class PhotosController extends AppController {

	var $name = 'Photos';
	var $components = array('RequestHandler');
	var $helpers = array('Xmlbuilder','Jsonbuilder');

	function index() {
		
		// URL Beispiele
		// http://localhost/cakephp/photos?id=2
		// http://localhost/cakephp/photos?tags=winter,eis&limit=10
		
		$allowedQryParams = array("id"=>"id");
		$allowedCtrlParams = array(
			"apikey" => "",
			"tags" => "",
			"geoframe" => "",  //TODO ====> sortby: "rating"=>"Rating.TODO"
			"sortby" => array("title"=>"Photo.title","created"=>"Photo.created","author"=>"Photo.user_name","rating"=>"Rating.TODO","views"=>"Photo.views"),
			"searchterm" => "",
			"offset" => 0,
			"limit" => 0,
			"format" => array("xml","json") //,"img","smallimg","thumbnail","tinyimg")
			);
		
		$invalidParams=false;
		$model="";
		$urlParams=array();
		
		$parsedParams=array();
		$parsedParams["format"]="html";
		$parsedParams["offset"]=$allowedCtrlParams["offset"];
		$parsedParams["limit"]=$allowedCtrlParams["limit"];
		$parsedParams["tags"]=array();
		$parsedParams["searchterm"]=array();
		$parsedParams["urlparams"]=array();
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
									array($model.'.title' => '%'.$val.'%'),
									array($model.'.description' => '%'.$val.'%')
									)
								);
								break;
							}
							case "tags":
							{	//TODO 	
								$val = preg_replace('/[^a-zA-Z0-9öÖüÜäÄß_,]/','',$val);
								$tags = preg_split('/,/',$val,-1,PREG_SPLIT_NO_EMPTY);
								if($tags) {
									//$tgs = array();
									$s = "";
									foreach ($tags as $tag) 
									{
										//$tgs = array_merge($tgs,array(array('Tag.tag_name' => $tag)));
										$s .= "'".$tag."',";
									}
									//$parsedParams["tags"] = array('AND' => $tgs);
									$parsedParams["tags"] = array("`Tag`.`tag_name` IN (".substr($s,0,strlen($s)-1).")");
								}
								break;
							}
							case "geoframe":
							{	//TODO
								echo "TODO: ".$key . " = " . $val . "<br />";
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
							{	//TODO
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
							{	//TODO
								echo "TODO: ".$key . " = " . $val . "<br />";
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
			
			// join photos <> tags
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
			
			// conditions/where-clause available OR null
			if ($parsedParams["urlparams"] || $parsedParams["searchterm"] || $parsedParams["tags"])
				$conditions = array('AND' => array(
					$parsedParams["urlparams"],
					$parsedParams["searchterm"],
					$parsedParams["tags"]
					));
			else $conditions = null; 
			
			// db request
			$result = $this->Photo->find('all', array(
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => $parsedParams["sortby"],
				'group' => array($model.'.id'),
				'limit' => $parsedParams["limit"]
				//,'page' => 2
			));
			
			switch ($parsedParams["format"]) 
			{
				case "html": 
					echo "<br />STATUS:<b><font color='green'>&nbsp;OK</font><b><br /><br />";
					$this->set("results",$result);
					break;
				case "json": 
					//TODO
					$this->set("results",$result);
					$this->render('\\'.$model.'s\json\index','\json\default',null);
					break;
				default: //"xml"
					$this->set("results",$result);
					$this->render('\\'.$model.'s\xml\index','\xml\default',null);
					break;
			}
		}
		else
		{
			echo "<br />STATUS:<b><font color='red'>&nbsp;INVALID-PARAMS !!</font><b><br /><br />";
			$this->set("results",null);
		}
		//$this->Photo->recursive = 0;
		//$this->set('photos', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid photo', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('photo', $this->Photo->read(null, $id));
	}

	function add() {
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
	}

	function edit($id = null) {
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
	}

	function delete($id = null) {
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
	}
}
?>