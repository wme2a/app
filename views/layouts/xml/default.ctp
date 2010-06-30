<?php
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header('Content-Type: application/xml');
header('Content-Type: text/xml');
echo $content_for_layout;
?>