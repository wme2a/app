<?php
class JsonbuilderHelper extends AppHelper {
		
	function photosToJson($results) 
	{	
		/*	JSON Object	
		{
			"description":"Represents one single photo. A photo has some metadata like URI, width, height and creation date, but also contains tags and rating information.",
			"type":"object",
			"properties":
			{
				"id":{"type":"number","description":"The photo's unique id."},
				"uri":{"type":"string","description":"A URI that points to the actual file. May be a relative path or an arbitrary URL."},
				"title":{"type":"string","description":"A title as chosen by the photo's author."},
				"created":{"type":"number","description":"The date when the photo was submitted to PhotonPainter. The format is UNIX timestamp."},
				"width":{"type":"number","description":"The photo's width in pixel."},
				"height":{"type":"number","description":"The photo's height in pixel."},
				"geo_lat":{"type":"number","description":"Latitude coordinate where the photo was taken (-90° - 90°). Can be null (not set)."},
				"geo_long":{"type":"number","description":"Longitude coordinate where the photo was taken (-180° - 180°). Can be null (not set)."},
				"aperture":{"type":"string","description":"Aperture setting for this photo. Can be null (not set)."},
				"exposuretime":{"type":"string","description":"Exposure time for this photo. Can be null (not set)."},
				"focallength":{"type":"string","description":"Focal length for this photo. Can be null (not set)."},
				"views":{"type":"number","description":"Indicates how many users have viewed this photo."},
				"author":{"type":"number","optional":true,"description":"ID of the user who uploaded this photo. Missing for external photos."},
				"description":{"type":"string","description":"A short description by the photo's author."},
				"tags":{"type":"string","description":"Contains all tags that have been added to this photo."}
			}
		}
		*/
		if (sizeof($results)>0)
		{
			$json = array();
			
			foreach ($results as $row) {
				$props = array();
				$props["id"] = intval($row['Photo']['id']);
				$props["uri"] = utf8_encode(""); // !!! TODO $row['Photo']['original_filename']
				$props["title"] = utf8_encode($row['Photo']['title']);
				$props["created"] = strtotime($row['Photo']['created']);
				$props["width"] = intval($row['Photo']['width']);
				$props["height"] = intval($row['Photo']['height']);
				$props["geo_lat"] = $row['Photo']['geo_lat'] != null ? doubleval($row['Photo']['geo_lat']) : null;
				$props["geo_long"] = $row['Photo']['geo_long'] != null ? doubleval($row['Photo']['geo_long']) : null;
				$props["aperture"] = $row['Photo']['aperture'] != null ? utf8_encode($row['Photo']['aperture']) : null;
				$props["exposuretime"] = $row['Photo']['exposuretime'] != null ? utf8_encode($row['Photo']['exposuretime']) : null;
				$props["focallength"] =  $row['Photo']['focallength'] != null ? utf8_encode($row['Photo']['focallength']) : null;
				$props["views"] = intval($row['Photo']['views']);
				if ($row['Photo']['user_id'] != null) $props["author"] = intval($row['Photo']['user_id']); //optional JSON attr
				$props["description"] = utf8_encode($row['Photo']['description']);
				$tags="";
				foreach ($row['Tag'] as $r) {
					$tags .= $r['tag_name'].",";
				}
				$props["tags"] = utf8_encode(substr($tags,0,strlen($tags)-1));
				array_push($json, $props); // style like JSON definition above
				//array_push($json, array("photo" => $props)); // style like JSON of example WS
			}
		}
		return $this->output(json_encode($json)); // style like JSON definition above
		//return $this->output(json_encode(array("photos" => $json))); // style like JSON of example WS
	}
	
	function commentsToJson($results) 
	{ 
		/*	JSON Object	
		{
			"description":"A comment on a photo, made by a user.",
			"type":"object",
			"properties":
			{
				"id":{"type":"number","description":"ID of this comment."},
				"title":{"type":"string","optional":true,"description":"The title of this comment. Can be null (not set)."},
				"user":{"type":"number","optional":true,"description":"ID of the user who submitted this comment. Null (missing) for anonymous comments."},
				"photo":{"type":"number","description":"ID of the photo this comment was submitted for."},
				"created":{"type":"number","description":"The date when the comment was submitted. The format is UNIX timestamp."},
				"comment":{"type":"string","description":"The body of this comment."}
			}
		}
		*/		
		if (sizeof($results)>0)
		{
			$json = array();
			foreach ($results as $r) {
				$properties = array();
				$properties["id"]=intval($r['Comment']['id']);
				$properties["title"] = $r['Comment']['title'] != null ? utf8_encode($r['Comment']['title']) : null;
				$properties["user"] = $r['Comment']['user_id'] != null ? intval($r['Comment']['user_id']) : null;
				$properties["photo"] = intval($r['Comment']['photo_id']);
				$properties["created"] = strtotime($r['Comment']['created']);
				$properties["comment"] = utf8_encode($r['Comment']['comment_text']);
				$properties["mod"] = $r['Comment']['modified'];
				array_push($json, $properties); // style like JSON definition above
				//array_push($json, array("comment" => $properties)); style like JSON of example WS
			}
		}
		return $this->output(json_encode($json)); // style like JSON definition above
		//return $this->output(json_encode(array("comments" => $json))); style like JSON of example WS
	}
	
	function tagsToJson($results) 
	{ 
		$json = array();
		if (sizeof($results)>0)
		{
		
		}
		return $this->output(json_encode($json));
	}
	
	function ratingsToJson($results) 
	{ 
		$json = array();
		if (sizeof($results)>0)
		{
	
		}
		return $this->output(json_encode($json));
	}
	
	function usersToJson($results) 
	{ 
		$json = array();
		if (sizeof($results)>0)
		{

		}
		return $this->output(json_encode($json));
	}

}
?>