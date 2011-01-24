<?php
/*
	ELSWAK MySQL Operator
	
	Operators are an integral part of a MySQL expression. Operators can have any
	value that is deemed a valid MySQL operator.
*/
class ELSWAK_MySQL_Operator_Exception extends ELSWAK_MySQL_Exception {}

class ELSWAK_MySQL_Operator
	extends ELSWAK_Settable {
	protected $operator;
	
	public function __construct($operator = '=') {
		$this->setOperator($operator);
	}
	public function setOperator($operator) {
		// set up a test value for string values
		$testValue = strtoupper($operator);
		
		// only allow the operator to be a valid operator
		if ($operator == '=') {
			$this->operator = '=';
		} else if ($operator == '<') {
			$this->operator = '<';
		} else if ($operator == '<=') {
			$this->operator = '<=';
		} else if ($operator == '=>') {
			$this->operator = '=>';
		} else if ($operator == '>') {
			$this->operator = '>';
		} else if ($operator == '!=') {
			$this->operator = '!=';
		} else if ($testValue == 'IS') {
			$this->operator = 'IS';
		} else if ($testValue == 'IS NOT') {
			$this->operator = 'IS NOT';
		} else if ($testValue == 'LIKE') {
			$this->operator = 'LIKE';
		} else if ($testValue == 'NOT LIKE') {
			$this->operator = 'NOT LIKE';
		} else if ($operator == '<=>') {
			$this->operator = '<=>';
		} else if ($operator == '<>') {
			$this->operator = '<>';
		} else {
			return new ELSWAK_MySQL_Operator_Exception('Unable to set operator. Input does not match any MySQL operator.');
		}
		return $this;
	}
	public function sql($format = '', $indent = '') {
		return $this->operator();
	}
}
