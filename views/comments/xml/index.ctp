<?php 
	if($results){
		$xs = $xmlbuilder->commentsToXml($results);
		?><center><textarea style="height:400px; width:96%; font-size:10pt; overflow:auto;" readOnly="readOnly"><?php echo $xs;?></textarea></center><?php
		if ($xmlbuilder->validate($xs))
		{ 
			?><br />XML is<b><font color='green'>&nbsp;VALID </font><b>!<br /><?php
		}
		else
		{
			?><br />XML is<b><font color='red'>&nbsp;INVALID </font><b>!<br /><?php
		}
	}
	else 
	{
		?><br /><b><font color='red'>&nbsp;No comment found that matched the search criteria. - 404 Error</font><b><br /><?php
	}
?>