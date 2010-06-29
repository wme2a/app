<?php 
	if ($results=="img")
	{
		//TODO ?><br />STATUS:<b><font color='green'>&nbsp;SHOW PHOTO Size: IMG - Errorcode 200</font><b><br /><br />
		<?php
	}
	if ($results=="smallimg")
	{
		//TODO ?><br />STATUS:<b><font color='green'>&nbsp;SHOW PHOTO Size: SMALLIMG - Errorcode 200</font><b><br /><br />
		<?php
	}
	if ($results=="thumbnail")
	{
		//TODO ?><br />STATUS:<b><font color='green'>&nbsp;SHOW PHOTO Size: THUMBNAIL - Errorcode 200</font><b><br /><br />
		<?php
	}
	if ($results=="tinyimg")
	{
		//TODO ?><br />STATUS:<b><font color='green'>&nbsp;SHOW PHOTO Size: TINYIMG - Errorcode 200</font><b><br /><br />
		<?php
	}
	
	if (!$results)
	{
		?><br />STATUS:<b><font color='red'>&nbsp;INVALID-PRECONDITION - Errorcode 412</font><b><br /><br /><?php
	}
?>