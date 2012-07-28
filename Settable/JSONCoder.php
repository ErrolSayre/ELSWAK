<?php
/*
	ELSWAK Settable JSONCoder
	
	This class utilizes the ELSWAK Settable class import/export methods to translate between JSON and PHP objects.
	Please note that a class must be provided in the constructor.
	Please note that the specified class must allow an empty constructor.
*/

class ELSWAK_Settable_JSONCoder_Exception extends ELSWAK_Data_Coder_Exception {}

class ELSWAK_Settable_JSONCoder
	implements ELSWAK_Data_Coder_Interface {
	
	protected $class;
	
	public function __construct($class) {
		if (is_subclass_of($class, 'ELSWAK_Settable')) {
			$this->class = $class;
		} else {
			throw new ELSWAK_Settable_JSONCoder_Exception('Unable to initilize coder. Provided class must implement ELSWAK Settable.');
		}
	}
	public function encode($data) {
		// make sure the data coming in is a settable object
		if ($data instanceof ELSWAK_Settable) {
			return json_encode($data->_export());
		}
		throw new ELSWAK_Settable_JSONCoder_Exception('Unable to encode data. Data must be an ELSWAK Settable object.');
	}
	public function decode($encoded) {
		$data = new $this->class;
		$data->_import(json_decode($encoded));
		return $data;
	}
}