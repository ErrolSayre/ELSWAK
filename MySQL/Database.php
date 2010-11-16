<?php
/*
	ELSWAK MySQL Database
	
	This class provides some basic information for an object representing the
	attributes of a database.
*/
class ELSWAK_MySQL_Database
	extends ELSWAK_Settable {
	protected $name;
	
	public function __construct($name) {
		$this->setName($name);
	}
	protected function setName($name) {
		$this->name = $name;
		return $this;
	}
	public function prettyName() {
		return $this->name;
	}
	public function sql($format = '', $indent = '') {
		return '`'.$this->name.'`';
	}
	public function __toString() {
		return $this->name();
	}
}