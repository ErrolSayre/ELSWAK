<?php
/*
	ELSWAK MySQL Literal
	
	A MySQL literal is an expression made up of a specific value. It also allows
	an hard coded string to be appended to a dynamic query.
*/
class ELSWAK_MySQL_Literal_Exception extends ELSWAK_MySQL_Exception {}

class ELSWAK_MySQL_Literal
	extends ELSWAK_Settable
	implements ELSWAK_MySQL_Expression {
	protected $literal;
	
	public function __construct($literal = '') {
		$this->setLiteral($literal);
	}
	public function setLiteral($literal) {
		// ensure that the literal contains only the allowable characters
		if (preg_match('/^[\w\+\-\.\(\)]*$/', $literal) === 1) {
			$this->literal = $literal;
		} else {
			throw new ELSWAK_MySQL_Literal_Exception('Unable to set literal. Literal does not fit the character set constraints.');
		}
		return $this;
	}
	public function sql($format = '', $indent = '') {
		return $this->literal;
	}
}
