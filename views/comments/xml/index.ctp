<?php 
	$model = $this->params["controller"];
	if($results && $xmlbuilder->validate($xmlbuilder->convertToXml($results, $model))) // validate xml to xsd
	{	// loading helper "xmlbuilder" and convert db result array to xml structure
		echo $xmlbuilder->convertToXml($results, $model);
	}
	else header("HTTP/1.0 404 Not Found");
?>