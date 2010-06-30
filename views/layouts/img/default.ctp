<?php
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header('Content-Type: image/*');
//header("Content-Description: File Transfer"); 
echo $content_for_layout;
?>