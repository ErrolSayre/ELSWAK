<?php
/*
	ELSWAK SerializedData Coder
	
	This class implements the coder interface with a backing of php serialization.
*/

class ELSWAK_Data_Serializer_Exception extends ELSWAK_Data_Coder_Exception {}

class ELSWAK_Data_Serializer
	implements ELSWAK_Data_Coder_Interface {
	
	public function encode($data, array $options = null) {
		return serialize($data);
	}
	public function decode($encoded, array $options = null) {
		if (($data = unserialize($encoded)) !== false) {
			return $data;
		}
		throw new ELSWAK_Data_Serializer_Exception('Unable to decode data: php unserialize failed.');
	}
}