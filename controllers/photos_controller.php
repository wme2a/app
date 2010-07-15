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
					$this->render('\\'.$model.'s\xml\index','\xml\default',null);
					break;
			}
		}
		else $this->set("results",null);
	}

	function add() 
	{
		if(array_key_exists("name", $this->params['url'])) $n = ($this->params['url']['name']);
		$teile = explode(".", $n);
		$putdata = fopen("php://input", "r");
		
		$fp = fopen(WWW_ROOT.'img/img/'.$n, "w");
		$size = 0;
		while ($data = fread($putdata, 51200))
		{
  			$fileOK = fwrite($fp, $data);
  			$size = $size + strlen($data);
  			if($size >= 10485760)
  			break;
		}
		fclose($fp);
		fclose($putdata);
		if($size >= 10485760)
		{
			unlink(WWW_ROOT.'img/img/'.$n);
							header("HTTP/1.0 412 Precondition Failed");
							echo "";
							return false;
		}
		if(file_exists(WWW_ROOT.'img/img/'.$n)) {
			$result = $this->Photo->findByOriginal_filename($n);
			$id = $result['Photo']['id'];
		}
			if($fileOK)
			{
				// read the metadata
				$exif_data = exif_read_data(WWW_ROOT.'img/img/'.$n,'EXIF',0);
				// set the metadata to array			
				if($id) $this->data['Photo']['id']= $id;
				$type = $exif_data['MimeType'];
				if($type == 'image/jpeg') $changedName = $teile[0].".jpg";
				if($type == 'image/png') $changedName = $teile[0].".png";
				rename(WWW_ROOT.'img/img/'.$n,WWW_ROOT.'img/img/'.$changedName);
				$this->data['Photo']['width']=$exif_data['COMPUTED']['Width'];
				$this->data['Photo']['height']=$exif_data['COMPUTED']['Height'];
				//list($x,$y)=explode('img/img/',$fileOK['url']);
				$this->data['Photo']['original_filename']=$changedName;	
				if(isset($exif_data['COMPUTED']['ApertureFNumber'])){
					$this->data['Photo']['aperture']=$exif_data['COMPUTED']['ApertureFNumber'];
					}
				if(isset($exif_data['ExposureTime']))
					$this->data['Photo']['exposuretime']=$exif_data['ExposureTime'].'s';
				if(isset($exif_data['FocalLength'])){
					list($x,$y)=explode("/1",$exif_data['FocalLength']);
					$this->data['Photo']['focallength']=$x.'mm';}
				if (isset($exif_data['GPSLatitude']))
				{	
						//convert Geo-data to decimal value					
						$lat = $this->exifGeoCoordinateToDecimal($exif_data["GPSLatitudeRef"], $exif_data["GPSLatitude"]);
						$lon = $this->exifGeoCoordinateToDecimal($exif_data["GPSLongitudeRef"], $exif_data["GPSLongitude"]);
						$this->data['Photo']['geo_lat']=$lat;
						$this->data['Photo']['geo_long']=$lon;
				}
				}
				//if upload succeseful save metadata of photo to database				
					$this->Photo->create();
					if (($this->Photo->save($this->data))) {
						$this->resizeImage(WWW_ROOT.'img/img/'.$changedName,WWW_ROOT.'img/smallimg/',400, 400);
						$this->resizeImage(WWW_ROOT.'img/img/'.$changedName,WWW_ROOT.'img/thumbnail/',120, 120);
						$this->resizeImage(WWW_ROOT.'img/img/'.$changedName,WWW_ROOT.'img/tinyimg/',50, 50);
						header("HTTP/1.0 201 Created");
						return true;
					} 
					else {
							unlink(WWW_ROOT.'img/img/'.$changedName);
							header("HTTP/1.0 412 Precondition Failed");
							echo "";
							return false;
						 }
	}

	function edit() 
	{
		$id = array_key_exists("id", $this->params['url']) ? intval($this->params['url']['id']) : null;
		
		if ($id && $this->Photo->findById($id)) // exists ? find photo /w id
		{
			//$xml_str='<pp:photo xmlns:pp="http://www-mmt.inf.tu-dresden.de/Lehre/Sommersemester_10/Vo_WME/Uebung/material/photonpainter" id="100" title="Catedral del buen pastor" width="1200" height="800" geo_lat="43.31721809" geo_long="-1.98207736000229" aperture="F/8" exposuretime="1/250s" focallength="24mm" views="0" user_name="MaNi" author="1"><pp:description>bla Blub</pp:description></pp:photo>';
			//$xml_str='<pp:photo xmlns:pp="http://www-mmt.inf.tu-dresden.de/Lehre/Sommersemester_10/Vo_WME/Uebung/material/photonpainter" title="Catedral del buen pastor" user_name="Keksi"><pp:description>bla Blub</pp:description></pp:photo>';
			//$xml_str='<pp:photo xmlns:pp="http://www-mmt.inf.tu-dresden.de/Lehre/Sommersemester_10/Vo_WME/Uebung/material/photonpainter"><pp:description></pp:description></pp:photo>';
			$xml_str = $this->httpStreamToString();	
			
			$model = ucfirst(substr($this->params["controller"],0,-1));
			
			App::import('Helper', 'Xmlbuilder');
			$x = new XmlbuilderHelper();
			if ($x->validate($xml_str)) // validate file stream source
			{
				$doc = new DOMDocument();
				$doc->preserveWhiteSpace = false;
				$doc->loadXML($xml_str);
				
				$xpath = new DOMXPath($doc);
				$tag = $xpath->query('//pp:photo')->item(0); // tag to write to db
				
				if ($tag) 
				{
					$edit = array();
					foreach ($tag->attributes as $k => $v) 
					{
						$edit[$model][$k] = $v->textContent; 
					}
					$edit[$model]["id"] = $id; // id from request params
					if ($tag->nodeValue != null) $edit[$model]["description"]  = $tag->nodeValue; // if null do not edit to fail @ save
					$edit[$model]["user_id"] = array_key_exists("author",$edit[$model]) ? $edit[$model]["author"] : null; // to force new id in db with create(); // to force new id in db with create()
					unset($edit[$model]["author"]);
					
					if ($this->Photo->save($edit)) // new id & save to db
					{
						$result = $this->Photo->findById($id);
						if ($result 
							&& $result[$model]["upload_complete"] == 0
							&& $result[$model]["title"] != null 
							&& $result[$model]["description"] != null 
							&& $result[$model]["user_name"] != null)
						{
							$this->Photo->saveField('upload_complete', 1); // to show that pic is uploaded & metadata is set
						}
						
						header("HTTP/1.0 201 Created");
						return true;
					}
				}
			}
		}
		header("HTTP/1.0 412 Precondition Failed");
		echo "";	
		return false;
	}

	function delete() 
	{
		$id = array_key_exists("id", $this->params['url']) ? intval($this->params['url']['id']) : null;
		
		$result = $this->Photo->findById($id); // just one result
		
		if ($id && $result)
		{
			if ($id && (sizeof($result) > 0) && $this->Photo->delete($id, true)) // delete cascaded
			{ 
				unlink(WWW_ROOT."img\img\\".$result['Photo']['original_filename']);
				unlink(WWW_ROOT."img\smallimg\\".$result['Photo']['original_filename']);
				unlink(WWW_ROOT."img\tinyimg\\".$result['Photo']['original_filename']);
				unlink(WWW_ROOT."img\thumbnail\\".$result['Photo']['original_filename']);
				return true;
			}
			else
			{
				header("HTTP/1.0 500 Internal Error");
				echo "";
				return false;
			}
		}
		
		header("HTTP/1.0 404 Not Found");
		echo "";
		return false;
	}
}
?>