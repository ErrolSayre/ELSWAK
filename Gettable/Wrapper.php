<?php
//!Functionality Stubs
// Place a stub for backward compatibility with PHP < 5.4.0
if (!interface_exists('JsonSerializable')) {
	require_once 'JsonSerializeableInterface.php';
}



/**
 * Wrap a value in a gettable object
 */
class ELSWAK_Gettable_Wrapper
	implements ELSWAK_Gettable, JsonSerializable {

	protected $value;
	
	public function __construct($value) {
		if (is_array($value)) {
			$value = implode(', ', $value);
		}
		elseif (is_object($value)) {
			if (method_exists($value, '__toString')) {
				$value = (string) $value;
			}
			else {
				$value = '';
			}
		}
		$this->value = (string) $value;
	}
	public function get($property = null) {
		return $this->value;
	}



//!JSONSerializable methods
	/**
	 * Provide the JSON encoder with an easy to handle array.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->value;
	}
	public function toJSON() {
		return json_encode($this->jsonSerialize());
	}
	public function __toString() {
		return $this->value;
	}
}