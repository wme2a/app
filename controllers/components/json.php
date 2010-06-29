<?php
class JsonComponent extends Object {
	var $name = 'Json';
	//var $properties = array();
		
	function commentToJS() {
		
		var $properties = array();
		$properties["id"]=1;
		$properties["title"]="tit";
		$properties["user"]=3;
		$properties["photo"]=4;
		$properties["created"]=122;
		$properties["comment"]="com";
		return $properties;
	}
	
}
?>