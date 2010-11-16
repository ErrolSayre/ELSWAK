<?php
/*
	ELSWAK MySQL Conjunction
*/
class ELSWAK_MySQL_Conjunction {
	protected $conjunction;
	
	public function __construct($conjunction = 'AND') {
		$this->setConjunction($conjunction);
	}
	public function conjunction() {
		return $this->conjunction;
	}
	public function setConjunction($conjunction) {
		if (strtoupper($conjunction) == 'OR') {
			$this->conjunction = 'OR';
		} else {
			$this->conjunction = 'AND';
		}
		return $this;
	}
	public function sql($format = '', $indent = '') {
		return $this->conjunction;
	}
}
