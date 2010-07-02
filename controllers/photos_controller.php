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
		$parsedParams["format"]="html";
		$parsedParams["urlparams"]=array();
		$parsedParams["offset"]=$allowedCtrlParams["offset"];
		$parsedParams["limit"]=$allowedCtrlParams["limit"];
		$parsedParams["tags"]=array();
		$parsedParams["searchterm"]=array();
		$parsedParams["geoframe"]=array();
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
								// style to parse: minLatitude,minLongitude,maxLatitude,maxLongitude
								$val = preg_replace('/[^0-9.,]/','',$val);
								$geoframe = preg_split('/,/',$val,-1,PREG_SPLIT_NO_EMPTY);
								if (sizeof($geoframe) == 4 && $geoframe[0] < $geoframe[2] && $geoframe[1] < $geoframe[3]) 
								{
									$parsedParams["geoframe"] = array('AND' => array(
										"Photo.geo_lat BETWEEN ".doubleval($geoframe[0])." AND ".doubleval($geoframe[2])."",
										"Photo.geo_long BETWEEN ".doubleval($geoframe[1])." AND ".doubleval($geoframe[3]).""
									));
								}
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
				
			// conditions/where-clause
			$conditions = array('AND' => array(
				array("Photo.upload_complete" => 1),
				$parsedParams["urlparams"],
				$parsedParams["searchterm"],
				$parsedParams["tags"],
				$parsedParams["geoframe"]
				));
			
			// db request
			$results = $this->Photo->find('all', array(
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => $parsedParams["sortby"],
				'group' => array($model.'.id'),
				'limit' => $parsedParams["limit"]
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
					$this->render('\\'.$model.'s\xml\index','xml\default',null);
					break;
			}
		}
		else $this->set("results",null);
	}

	function add() {
		echo "TODO add";
	}

	function edit() {
		echo "TODO edit";
	}

	function delete() 
	{
		$id = array_key_exists("id", $this->params['url']) ? intval($this->params['url']['id']) : null;
		
		$result = $this->Photo->findById($id); // just one result
		
		if ($id && $this->Photo->delete($id, true)) // delete cascaded
		{ 
			unlink(WWW_ROOT."img\img\\".$result['Photo']['original_filename']);
			unlink(WWW_ROOT."img\smallimg\\".$result['Photo']['original_filename']);
			unlink(WWW_ROOT."img\tinyimg\\".$result['Photo']['original_filename']);
			unlink(WWW_ROOT."img\thumbnail\\".$result['Photo']['original_filename']);
			return true;
		}
		header("HTTP/1.0 404 Not Found");
		echo "";
		return false;
	}
}
?>