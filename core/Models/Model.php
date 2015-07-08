<?php
namespace TypeRocket\Models;

abstract class Model {

	public $item_id = null;
	public $action = null;
	public $fields = null;
	public $valid = null;
	public $defaults = null;
	public $statics = null;

	function save($item_id, $action = 'update' ) {

		$this->fields  = isset($_POST['tr']) ? $_POST['tr'] : array();
		$this->item_id = $item_id;
		$this->action = $action;

		if($this->validate()) {
			$this->sanitize();

			if($this->action === 'update') {
				$this->update();
			} else {
				$this->create();
			}

		}

        return $this;

	}

	function validate() {
		return $this->valid = true;
	}

	function sanitize() { }

	protected function update() { }
	protected function create() { }

}