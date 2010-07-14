<?php

class AppController extends Controller
{
	public function beforeFilter()
	{
		if ($this->params["controller"] == "pages")
		{
			switch(substr($this->params["url"]["url"],-3)) // parse file extension
			{
				case "xsd": 
					$this->layout = '/xml/default';
					break;
				case "xml": 
					$this->layout = '/xml/default';
					break;
			}
		}
	}
   
	/**
 * convert Geo-Inforamtion in exif to decimal value
 * @params:
 *		$reference 	= reference to the earth-side (N or S)
 *		$coordinate 	= geo coordinate array in exif-header of photos
 * @return:
 *		decimal value
 */
      function exifGeoCoordinateToDecimal($reference, array $coordinate)
      {
      list($a,$b) = explode("/", $coordinate[0]);
      list($c,$d) = explode("/", $coordinate[1]);
      list($e,$f) = explode("/", $coordinate[2]);
      $prefix = $reference == 'S' || $reference == 'W' ? -1 : 1;
      $a = $b > 0 ? $a / $b : $a;
      $c = $d > 0 ? $c / $d : $c;
      $e = $f > 0 ? $e / $f : $e;
        return $prefix * round(($a + ($c * 60 + $e) / 3600), 6);
      }
      
	function httpStreamToString() {
	
		/* PUT data comes in on the stdin stream */
		$putdata = fopen("php://input", "r");
		
		$str = "";
		while ($data = fread($putdata, 1024)) { // Read the data 1 KB at a time and write to the file
			$str .= $data;
		}
		/* Close the stream */
		fclose($putdata);
		
		return $str;
	}
	
	function resizeImage($originalImage,$folder,$toWidth,$toHeight)
	{
    list($width, $height) = getimagesize($originalImage);
    $xscale=$width/$toWidth;
    $yscale=$height/$toHeight;

    if ($yscale>$xscale){
        $new_width = round($width * (1/$yscale));
        $new_height = round($height * (1/$yscale));
    }
    else {
        $new_width = round($width * (1/$xscale));
        $new_height = round($height * (1/$xscale));
    }
  	$system=explode('.',$originalImage);
  	$part = explode('img/img/',$system[0]);
	if (preg_match('/jpg|jpeg/',$system[1])){
		$src_img=imagecreatefromjpeg($originalImage);
	}
	if (preg_match('/png/',$system[1])){
		$src_img=imagecreatefrompng($originalImage);
	}
    $dst_img = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	if (preg_match("/png/",$system[1]))
	{
		imagepng($dst_img,$folder.$part[1].".png"); 
	} else {
		imagejpeg($dst_img,$folder.$part[1].".jpg"); 
	}
	imagedestroy($dst_img); 
	imagedestroy($src_img); 
	} 
} 
?>