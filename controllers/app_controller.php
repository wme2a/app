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
	/**
 * uploads files to the server
 * @params:
 *		$folder 	= the folder to upload the files e.g. 'img/img'
 *		$file 	= the array containing the form files
 *		$itemId 	= id of the item (optional) will create a new sub folder
 * @return:
 *		will return an array with the success of each file upload
 */
	function uploadFile($folder, $file, $itemId = null) {
	// setup dir names absolute and relative
	$folder_url = WWW_ROOT.$folder;
	$rel_url = $folder;
	// create the folder if it does not exist
	if(!is_dir($folder_url)) {
		mkdir($folder_url);
	}
		
	// if itemId is set create an item folder
	if($itemId) {
		// set new absolute folder
		$folder_url = WWW_ROOT.$folder.'/'.$itemId; 
		// set new relative folder
		$rel_url = $folder.'/'.$itemId;
		// create directory
		if(!is_dir($folder_url)) {
			mkdir($folder_url);
		}
	}
		// replace spaces with underscores
		$filename = str_replace(' ', '_', $file['name']);
		// list of permitted file types
		$permitted = array('image/gif','image/jpeg','image/png');	
		// assume filetype is false
		$typeOK = false;
		$filetype = $file['type'];		
		// check filetype is ok
		if(in_array($filetype,$permitted))
		{
			$typeOK=true;
		}
		// if file type ok upload the file
		if($typeOK) {
			// switch based on error code
					switch($file['error']) {
				case 0:
					// check filename already exists
					if(!file_exists($folder_url.'/'.$filename)) {
						// create full filename
						$full_url = $folder_url.'/'.$filename;
						$url = $rel_url.'/'.$filename;
						// upload the file
						$success = move_uploaded_file($file['tmp_name'], $url);
					} else {
						// create unique filename and upload file
						ini_set('date.timezone', 'Europe/Berlin');
						$now = date('Y-m-d-His');
						$full_url = $folder_url.'/'.$now.$filename;
						$url = $rel_url.'/'.$now.$filename;
						$success = move_uploaded_file($file['tmp_name'], $url);
					}
					// if upload was successful
					if($success) {
						// save the url of the file
						$result['url'] = $url;
					} else {
						$result['error'] = "Error uploaded $filename. Please try again.";
					}
					break;
				case 3:
					// an error occured
					$result['error'] = "Error uploading $filename. Please try again.";
					break;
				default:
					// an error occured
					$result['error'] = "System error uploading $filename. Contact webmaster.";
					break;
			}
		} elseif($file['error'] == 4) {
			// no file was selected for upload
			$result['nofile'] = "No file Selected";
		} else {
			// unacceptable file type
			$result['error'] = "$filename cannot be uploaded. Acceptable file types: gif, jpg, png.";
		}
		return $result;
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
} 
?>