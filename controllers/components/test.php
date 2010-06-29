<?php

class TestComponent extends Object {

	// Diese Komponente nutzt andere Komponenten
	var $components = array('Session');
		
	function doStuff() {
		$this->Session->write('xml', 'test');
	}
	
}
?>