<?php
class RatingsController extends AppController {

	var $name = 'Ratings';

	function index() {
		$this->Rating->recursive = 0;
		echo "DEBUGGING <br />-------------<br />";
	
	$urlParams = array();
	$model = " ";
	
	foreach ($this->params['url'] as $key => $val) {
		if ($key=="url") {
			$model=ucfirst($val); // first letter uppercase
			$model=substr(ucfirst($val),0,strlen($model)-1);
			echo $key . " = " . $val . "<br />";
		}
		else {
			$urlParams[$key]=$val;
			echo $key . " = " . $val . "<br />";
		}
	}
	if ($urlParams) {
		// generates conditions from GET params
		$cond = "";
		foreach	($urlParams as $k => $v) {
			$cond .= $model . "." . $k . " = " . $v . " AND ";
		}
		$cond = substr($cond,0,strlen($cond)-5); //delete last " AND "
		// debugging
		echo "§model = " . $model . " | §cond = " . $cond . "<br/>";
		// db request
		$eintraege = $this->Rating->find('all', array(
			'conditions' => array($cond)
			
		));
	}
	else {
		$eintraege = $this->Rating->find('all');
	}
	
	$this->set('eintraege',$eintraege);
	$this->set('test',$urlParams); //debugging
		$this->set('ratings', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid rating', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('rating', $this->Rating->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Rating->create();
			if ($this->Rating->save($this->data)) {
				$this->Session->setFlash(__('The rating has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The rating could not be saved. Please, try again.', true));
			}
		}
		$photos = $this->Rating->Photo->find('list');
		$users = $this->Rating->User->find('list');
		$this->set(compact('photos', 'users'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid rating', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Rating->save($this->data)) {
				$this->Session->setFlash(__('The rating has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The rating could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Rating->read(null, $id);
		}
		$photos = $this->Rating->Photo->find('list');
		$users = $this->Rating->User->find('list');
		$this->set(compact('photos', 'users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for rating', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Rating->delete($id)) {
			$this->Session->setFlash(__('Rating deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Rating was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>