<?php
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header('Content-Type: application/x-json');
//header("Content-Description: File Transfer"); 
//header("X-JSON: ".$content_for_layout);
echo $content_for_layout;
?>