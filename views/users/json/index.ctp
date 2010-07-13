<?php
	$model = $this->params["controller"];
	header('Content-Disposition: attachment; filename="'.$model.'"');         
	if($results)
	{	// loading helper "xmlbuilder" and convert db result array to json structure
		echo $jsonbuilder->convertToJson($results, $model);
	}
	else header("HTTP/1.0 404 Not Found"); 
?>