<?php 
	if($results){
		$xs = $jsonbuilder->commentsToJson($results);
		?><center><textarea style="height:400px; width:96%; font-size:10pt; overflow:auto;" readOnly="readOnly"><?php echo $xs;?></textarea></center><?php
	}
	else 
	{
		?><br /><b><font color='red'>&nbsp;No comment found that matched the search criteria. - 404 Error</font><b><br /><?php
	}
?>