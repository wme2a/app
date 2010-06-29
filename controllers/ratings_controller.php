<?php
class RatingsController extends AppController {

	var $name = 'Ratings';

	function index() {
		$this->Rating->recursive = 0;
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