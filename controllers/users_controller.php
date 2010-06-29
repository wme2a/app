<?php
class UsersController extends AppController {

	var $name = 'Users';

	function index() {
		$this->User->recursive = 0;
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
		$eintraege = $this->User->find('all', array(
			'conditions' => array($cond)
			
		));
	}
	else {
		$eintraege = $this->User->find('all');
	}
	
	$this->set('eintraege',$eintraege);
	$this->set('test',$urlParams); //debugging
		$this->set('users', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->flash(__('Invalid user', true), array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->flash(__('User saved.', true), array('action' => 'index'));
			} else {
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->flash(sprintf(__('Invalid user', true)), array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->flash(__('The user has been saved.', true), array('action' => 'index'));
			} else {
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->flash(sprintf(__('Invalid user', true)), array('action' => 'index'));
		}
		if ($this->User->delete($id)) {
			$this->flash(__('User deleted', true), array('action' => 'index'));
		}
		$this->flash(__('User was not deleted', true), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
	function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->flash(__('Invalid user', true), array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->flash(__('User saved.', true), array('action' => 'index'));
			} else {
			}
		}
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->flash(sprintf(__('Invalid user', true)), array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->flash(__('The user has been saved.', true), array('action' => 'index'));
			} else {
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->flash(sprintf(__('Invalid user', true)), array('action' => 'index'));
		}
		if ($this->User->delete($id)) {
			$this->flash(__('User deleted', true), array('action' => 'index'));
		}
		$this->flash(__('User was not deleted', true), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
?>