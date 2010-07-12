<?php
/*
	ELSWAK Exception List
	
	This class provides a method by with to collect and return a list of exceptions.
	Primarily the expected use case is for methods which accept a "list of things" and validates and acts upon each separately. Utilizing this mechanism individual items can throw exceptions while processing the subsequent items in the list can continue.
*/
class ELSWAK_ExceptionList
	extends Exception {
	protected $exceptions;
	
	public function __construct($import = null, $message = '', $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
		$this->setExceptions($import);
	}
	public function add(Exception $e) {
		$this->exceptions[] = $e;
		return $this;
	}
	public function exceptions() {
		return $this->exceptions;
	}
	public function hasExceptions() {
		return count($this->exceptions) > 0;
	}
	protected function setExceptions($value) {
		if ($value instanceof Exception) {
			$this->add($value);
		} else if (is_array($value)) {
			$errors = new ELSWAK_ExceptionList(null, 'Unable to set exceptions.');
			foreach ($value as $key => $item) {
				if ($item instanceof Exception) {
					$this->add($item);
				} else {
					$errors->add(new ELSWAK_ExceptionList_InvalidItem_Exception('Unable to add item with key: “'.$key.'”. Item must be an Exception.'));
				}
			}
			if ($errors->hasExceptions()) {
				throw $errors;
			}
		} else if ($value != null) {
			throw new ELSWAK_ExceptionList_InvalidItem_Exception('Unable to add item. Item must be an exception');
		}
	}
}
class ELSWAK_ExceptionList_InvalidItem_Exception extends ELSWAK_Exception {}