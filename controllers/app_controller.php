<?php
class AppController extends Controller {
	var $components  = array('RequestHandler');
	
	function beforeFilter() {
	
		//$this->RequestHandler->setContent('xml', 'application/xml');
		//$this->RequestHandler->respondAs('xml') ;
	}
}
?>