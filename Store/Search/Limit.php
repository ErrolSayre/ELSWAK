<?php
/*
	ELSWAK Store Search Limit
*/
class ELSWAK_Store_Search_Limit
	extends ELSWAK_Settable {
	protected $count;
	protected $offset;
	
	public function __construct($count = 0, $offset = 0) {
		$this->setCount($count);
		$this->setOffset($offset);
	}
	public function setCount($count) {
		$this->count = intval($count);
		return $this;
	}
	public function setOffset($offset) {
		$this->offset = intval($offset);
		return $this;
	}
}