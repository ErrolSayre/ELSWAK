<?php
/*
	ELSWAK JSON Data Coder
	
	This class implements the coder interface with a backing of JSON encoding.
	
	Due to the behavior of json_decode, this class throws no exceptions, unlike the PHP Serializer.
*/

class ELSWAK_JSON_Data_Coder
	implements ELSWAK_Data_Coder_Interface {
	
	public function encode($data, array $options = null) {
		return json_encode($data, $options);
	}
	public function decode($encoded, array $options = null) {
		$returnAssociativeArray = false;
		$maximumRecursionDepth = 512;
		$jsonOptions = 0;
		if (is_array($options)) {
			if (array_key_exists('assoc', $options)) {
				$returnAssociativeArray = ($options['assoc']);
			}
			if (array_key_exists('depth', $options)) {
				$maximumRecursionDepth = $options['depth'];
			}
			if (array_key_exists('options', $options)) {
				$jsonOptions = $options['options'];
			}
		}
		return json_decode($encoded, $returnAssociativeArray, $maximumRecursionDepth, $jsonOptions);
	}
}