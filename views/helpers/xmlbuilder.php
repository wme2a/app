<?php
class XmlbuilderHelper extends AppHelper {
		
	var $helpers = array('Xml');
	
	function photosToXml($results) 
	{	
		$xs = "";
		if (sizeof($results)>0)
		{
			$x = $this->Xml;
			$xs .= $x->header();
			$x->addNS('pp', 'http://www-mmt.inf.tu-dresden.de/Lehre/Sommersemester_10/Vo_WME/Uebung/material/photonpainter');
			$xs .=  $x->elem('pp:photos', null, null, false).">"; // .">" needed to close the tag, because the tag stays open, if content == null
			foreach ($results as $row) {
				$row['Photo']['author'] = $row['Photo']['user_id']; // add attr author (rename user_id to author)
				unset($row['Photo']['user_id']); // remove attr user_id (rename user_id to author)
				$description = $row['Photo']['description']; // set $description for content
				unset($row['Photo']['description']); // remove attr description
				$row['Photo']['created'] = strtotime($row['Photo']['created']); // based on xsd validation
				$row['Photo']['geo_lat'] = doubleval($row['Photo']['geo_lat']); // based on xsd validation
				$row['Photo']['geo_long'] = doubleval($row['Photo']['geo_long']); // based on xsd validation
				unset($row['Photo']['original_filename']); // based on xsd validation
				unset($row['Photo']['upload_complete']); // based on xsd validation
				$xs .=  $x->elem('pp:photo', $row['Photo'], null, false).">";
					$xs .=  $x->elem('pp:description', null, $description, false); // $description as content
					$xs .=  $x->closeElem();
					$xs .=  $x->elem('pp:tags', null, null, false).">"; // .">" needed to close the tag, because the tag stays open, if content == null
						foreach ($row['Tag'] as $r) {
							unset($r['id']); // based on xsd validation
							unset($r['photo_id']); // based on xsd validation
							$tag_name = $r['tag_name'];
							unset($r['tag_name']); // based on xsd validation
							$xs .=  $x->elem('pp:tag', $r, $tag_name, false);
							$xs .=  $x->closeElem();
						}
					$xs .=  $x->closeElem();
					
					$xs .=  $x->elem('pp:ratings', null, null, false).">"; // .">" needed to close the tag, because the tag stays open, if content == null	
						foreach ($row['Rating'] as $r) {
							$xs .=  $x->elem('pp:rating', $r, null, false).">";
							$xs .=  $x->closeElem();
						}
					$xs .=  $x->closeElem();	
						
					$xs .=  $x->elem('pp:comments', null, null, false).">";
						foreach ($row['Comment'] as $r) {
							$comment_text = $r['comment_text']; // set $comment_text for content
							unset($r['comment_text']); // remove attr comment_text
							$r['created'] = strtotime($r['created']); // based on xsd validation
							unset($r['modified']); // based on xsd validation
							$xs .=  $x->elem('pp:comment', $r, $comment_text, false); // $comment_text as content
							$xs .=  $x->closeElem();
						}
					$xs .=  $x->closeElem();	
					
					
				$xs .=  $x->closeElem();
			}
			$xs .=  $x->closeElem();
			$xs = $this->formatOutput($xs);
		}
		return $this->output($xs);
	}
	
	function commentsToXml($results) 
	{ 
		$xs = "";
		if (sizeof($results)>0)
		{
			$x = $this->Xml;
			$xs .= $x->header();
			$x->addNS('pp', 'http://www-mmt.inf.tu-dresden.de/Lehre/Sommersemester_10/Vo_WME/Uebung/material/photonpainter');
				$xs .=  $x->elem('pp:comments', null, null, false).">"; // .">" needed to close the tag, because the tag stays open, if content == null
					foreach ($results as $r) {
						$comment_text = $r['Comment']['comment_text']; // set comment_text as/for content
						unset($r['Comment']['comment_text']); // remove attr comment_text
						unset($r['Comment']['modified']); // remove attr modified
						$r['Comment']['created'] = strtotime($r['Comment']['created']); // based on xsd validation
						$xs .=  $x->elem('pp:comment', $r['Comment'], $comment_text, false); // $comment_text as content
						$xs .=  $x->closeElem();
					}
				$xs .=  $x->closeElem();
				$xs = $this->formatOutput($xs);
		}
		return $this->output($xs);
	}
	
	function tagsToXml($results) 
	{ 
		$xs = "";
		if (sizeof($results)>0)
		{
			$x = $xml;
			$xs .= $x->header();
			$x->addNS('pp', 'http://www-mmt.inf.tu-dresden.de/Lehre/Sommersemester_10/Vo_WME/Uebung/material/photonpainter');
			//TODO
			//place some xml structure here ...
			$xs = $this->formatOutput($xs);
		}
		return $this->output($xs);
	}
	
	function ratingsToXml($results) 
	{ 
		$xs = "";
		if (sizeof($results)>0)
		{
			$x = $xml;
			$xs .= $x->header();
			$x->addNS('pp', 'http://www-mmt.inf.tu-dresden.de/Lehre/Sommersemester_10/Vo_WME/Uebung/material/photonpainter');
			//TODO
			//place some xml structure here ...
			$xs = $this->formatOutput($xs);
		}
		return $this->output($xs);
	}
	
	function usersToXml($results) 
	{ 
		$xs = "";
		if (sizeof($results)>0)
		{
			$x = $xml;
			$xs .= $x->header();
			$x->addNS('pp', 'http://www-mmt.inf.tu-dresden.de/Lehre/Sommersemester_10/Vo_WME/Uebung/material/photonpainter');
			//TODO
			//place some xml structure here ...
			$xs = $this->formatOutput($xs);
		}
		return $this->output($xs);
	}
	
	function formatOutput($xmlstr)
	{
		$d = new DOMDocument('1.0', 'utf-8');
		$d->loadXML(utf8_encode($xmlstr));
		$d->formatOutput=true;
		return $d->saveXML();
	}

	function validate($xmlstr, $xmlschema='')
	{
		if (strlen($xmlstr)>0)
		{
			$xdoc = new DOMDocument('1.0', 'utf-8');
			$xmlschema = '../webroot/files/photonpainter.xsd';
			$xdoc->loadXML($xmlstr);
			//Validate the XML file against the schema
			if ($xdoc->schemaValidate($xmlschema)) {
				return $this->output(true); // is valid
			}
		}
		return $this->output(false); // is invalid
	}
	
}
?>