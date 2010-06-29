<?php
class TagsController extends AppController {

	var $name = 'Tags';

	function index() {
		$this->Tag->recursive = 0;
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
		$eintraege = $this->Tag->find('all', array(
			'conditions' => array($cond)
			
		));
	}
	else {
		$eintraege = $this->Tag->find('all');
	}
	
	$this->set('eintraege',$eintraege);
	$this->set('test',$urlParams); //debugging
		$this->set('tags', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid tag', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('tag', $this->Tag->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Tag->create();
			if ($this->Tag->save($this->data)) {
				$this->Session->setFlash(__('The tag has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tag could not be saved. Please, try again.', true));
			}
		}
		$photos = $this->Tag->Photo->find('list');
		$this->set(compact('photos'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid tag', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Tag->save($this->data)) {
				$this->Session->setFlash(__('The tag has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tag could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Tag->read(null, $id);
		}
		$photos = $this->Tag->Photo->find('list');
		$this->set(compact('photos'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for tag', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Tag->delete($id)) {
			$this->Session->setFlash(__('Tag deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Tag was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>