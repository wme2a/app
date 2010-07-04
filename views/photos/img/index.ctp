<?php 
	if ($results) 
	{	
		$img_path = WWW_ROOT."\img\\" . $results["imgtype"] . "\\" . $results[0]["Photo"]["original_filename"];
		
		header('Content-Type: ' . image_type_to_mime_type(exif_imagetype($img_path)));
		header('Content-Disposition: filename="'.$results[0]["Photo"]["original_filename"].'"'); 
		
		switch (image_type_to_extension(exif_imagetype($img_path)))
		{
			case ".jpeg":
				imagejpeg(imagecreatefromjpeg($img_path));
				break;
			case ".png":
				imagepng(imagecreatefrompng($img_path));
				break;
			default:
				header("HTTP/1.0 404 Not Found");
				echo "";
				break;
		}
	}
	else
	{
		header("HTTP/1.0 412 Precondition Failed");
		echo "";
	}
?>