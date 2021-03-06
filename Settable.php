<?php
/*
ELSWAK Settable
	
The Settable base class seeks to provide two main features:
	• allow direct access to properties which require computational action during getter/setter operations normally requiring access to these properties through accessor methods.
	• allow consistent access to model properties without the need to excplicitly write accessors for all properties.
Additionally this class provides:
	• protection of the model declaration - properties can not be added/removed at run-time outside the model itself.
	• virtual properties - methods that generate a value can be treated as a property.
	• protected properties - if properties need to be protected members, simply write explicit protected accessor methods.
	
By utilizing the magic methods __get, __set, and __call, Settable allows model properties to be accessed either directly or via accessor methods without additional programming in the model. In this way, properties which require operations (e.g. validating proper input ranges in a set operation) during access can do so transparently without the client code needing to know that some properties require the use of an accessor method while others do not.
	
This class utilizes the following conventions:
	• setter methods should be the name of the property with "set" appended to the beginning of the name.
	• getter methods should be the name of the property with or without "get" appended to the beginning of the name. (Either approach is supported.)
	• a property name can be utilized as a getter or a setter by calling the method with no arguments (or more than one) to operate as a getter or with a single argument to operate as a setter. This functionality will only be provided if no method with the matching name exists and the method call matches the case of the property name exactly or matches the get and set prefix conventions.
*/

require 'StandardConstants.php';

class ELSWAK_Settable
	extends ELSWAK_Object {
	
	public function __toString() {
		return $this->_describe();
	}
	public function _describe($padding = '', $json = false) {
		$values = array();
		$keys = array_keys(get_object_vars($this));
		foreach ($keys as $property) {
			try {
				$value = $this->{$property};
				if ($value instanceof ELSWAK_Settable) {
					$value = $value->_describe($padding.TAB, $json);
				} else {
					$value = json_encode($value);
				}
				if ($json) {
					$values[] = '"'.$property.'": '.$value;
				} else {
					$values[] = $property.': '.$value;
				}
			} catch (Exception $e) {}
		}
		if ($json) {
			return '{'.LF
				.$padding.TAB.implode(','.LF.$padding.TAB, $values).LF
				.$padding.'}';
		}
		return get_class($this).' {'.LF
			.$padding.TAB.implode(','.LF.$padding.TAB, $values).LF
			.$padding.'}';
	}
	
	
	
//!Array Property Methods
	protected function _setArrayProperty($property, $value) {
		$this->{$property} = array();
		if (is_array($value)) {
			foreach ($value as $item) {
				$this->_addArrayPropertyItem($property, $item);
			}
		} else if ($value != null) {
			$this->_addArrayPropertyItem($property, $value);
		}
		return $this;
	}
	protected function _setKeyedArrayProperty($property, $value) {
		$this->{$property} = array();
		if (is_array($value)) {
			foreach ($value as $key => $item) {
				$this->_setArrayPropertyItemForKey($property, $key, $item);
			}
		}
		return $this;
	}
	protected function _arrayPropertyHasItems($property) {
		return count($this->{$property}) > 0;
	}
	protected function _addArrayPropertyItem($property, $value) {
		// validate the value if applicable
		// determine if a verify method exists
		if ($this->_methodExists('_verify'.$property.'Item')) {
			if (!$this->{'_verify'.$property.'Item'}($value)) {
				throw new Exception('Unable to add item to '.$property.'. Provided value is invalid.');
			}
		}
		$this->{$property}[] = $value;
		return $this;
	}
	public static function _arrayItemForKey($array, $key) {
		if (isset($array[$key])) {
			return $array[$key];
		}
		return null;
	}
	public static function _arrayKeyForValue($array, $value, $ignoreCase = true) {
		if ($ignoreCase)
			$value = strtolower($value);
		foreach ($array as $key => $item) {
			if ($ignoreCase) {
				$item = strtolower($item);
			}
			if ($item == $value) {
				return $key;
			}
		}
		return null;
	}
	protected function _arrayPropertyItemForKey($property, $key) {
		return $this->_arrayItemForKey($this->{$property}, $key);
	}
	protected function _arrayPropertyHasItemForKey($property, $key) {
		if ($this->_methodExists('_verify'.$property.'Key')) {
			if (!$this->{'_verify'.$property.'Key'}($key)) {
				throw new Exception('Unable to set '.$property.' for key “'.$key.'”. Supplied key does not match accepted keys list.');
			}
		}
		if (isset($this->{$property}[$key])) {
			if ($this->_methodExists('_verify'.$property.'Item')) {
				if ($this->{'_verify'.$property.'Item'}($this->{$property}[$key])) {
					return true;
				}
			}
			return empty($this->{$property}[$key]);
		}
		return false;
	}
	protected function _setArrayPropertyItemForKey($property, $key, $value) {
		// validate the key if applicable
		if ($this->_methodExists('_verify'.$property.'Key')) {
			if (!$this->{'_verify'.$property.'Key'}($key)) {
				throw new Exception('Unable to set '.$property.' for key “'.$key.'”. Supplied key does not match accepted keys list.');
			}
		}
		// validate the value if applicable
		if ($this->_methodExists('_verify'.$property.'Item')) {
			if (!$this->{'_verify'.$property.'Item'}($value)) {
				throw new Exception('Unable to set '.$property.' for key “'.$key.'”. Provided value is invalid.');
			}
		}
		$this->{$property}[$key] = $value;
		return $this;
	}
	protected function _removeArrayPropertyItemForKey($property, $key) {
		// utilize the remove and return item method but return $this for backward compatibility
		$this->_removeAndReturnArrayPropertyItemForKey($property, $key);
		return $this;
	}
	protected function _removeAndReturnArrayPropertyItemForKey($property, $key) {
/*
	Remove an item and return it to the caller.
	
	Due to the prior remove method, this method required a different name to provide the differing behavior.
*/
		$value = null;
		if (array_key_exists($key, $this->{$property})) {
			$value = $this->{$property}[$key];
			unset($this->{$property}[$key]);
		}
		return $value;
	}
	protected function _arrayPropertyKeys($property) {
		return array_keys($this->{$property});
	}
	protected function _arrayPropertyCount($property) {
		return count($this->{$property});
	}
	protected function _verifyArrayPropertyKey($property, $key) {
		if ($this->_methodExists('_list'.$property.'Keys')) {
			$keys = $this->{'_list'.$property.'Keys'}();
			if (in_array($key, $keys)) {
				return true;
			}
		}
		return false;
	}
//!Scalar Properties
	protected function _setPropertyAsString($property, $value) {
		$this->{$property} = strval($value);
		return $this;
	}
	protected function _setPropertyAsInteger($property, $value) {
		$this->{$property} = intval($value);
		return $this;
	}
	protected function _setPropertyAsPositiveInteger($property, $value) {
		$value = intval($value);
		if ($value >= 0) {
			$this->{$property} = $value;
		}
		return $this;
	}
	protected function _setPropertyAsId($property, $value) {
		return $this->_setPropertyAsPositiveInteger($property, $value);
	}
	protected function _setPropertyAsBoolean($property, $value) {
		$this->{$property} = $this->valueAsBoolean($value);
		return $this;
	}
	protected function _setPropertyAsNullBoolean($property, $value) {
		$this->{$property} = $this->valueAsNullBoolean($value);
		return $this;
	}
	protected function _setPropertyAsStringOfMaximumLength($property, $value, $length = 255) {
		if (($value = substr($value, 0, $length)) !== false) {
			$this->{$property} = $value;
		} else {
			$this->{$property} = '';
		}
		return $this;
	}
	protected function _setPropertyAsTimestamp($property, $value) {
		if (is_numeric($value)) {
			$this->{$property} = intval($value);
		} else if (str_replace(array('/', '-', ':', ' '), '', $value) == 0) {
			$this->{$property} = 0;
		} else {
			$this->{$property} = strtotime($value);
		}
		return $this;
	}
	protected function _setPropertyAsFloat($property, $value) {
		$this->{$property} = floatval($value);
		return $this;
	}
	protected function _setPropertyAsDollarAmount($property, $value) {
		$this->{$property} = round(floatval(str_replace(array(',', '$'), '', $value)), 2);
		return $this;
	}
	protected function _getPropertyAsDollarAmount($property) {
		return sprintf('%.2f', floatval(str_replace(array(',', '$'), '', $this->{$property})));
	}
	protected function _setPropertyAsEnumeratedValue($property, $value, $values, $ignoreCase = true) {
		$this->{$property} = $this->_validateValueAgainstList($value, $values, $ignoreCase);
		return $this;
	}
	protected static function _validateValueAgainstList($value, $values, $ignoreCase = true, $returnKey = false) {
		if ($ignoreCase)
			$value = strtolower($value);
		foreach ($values as $validKey => $validValue) {
			$compareValue = $validValue;
			if ($ignoreCase)
				$compareValue = strtolower($compareValue);
			if ($value == $compareValue) {
				return $returnKey? $validKey: $validValue;
			} else if (!is_numeric($validKey)) {
				// try to compare the value against the key
				$compareValue = $validKey;
				if ($ignoreCase)
					$compareValue = strtolower($compareValue);
				if ($value == $compareValue) {
					return $returnKey? $validKey: $validValue;
				}
			}
		}
		throw new Exception('Supplied value does not match accepted values list.');
	}
	protected static function _keyForValueValidatedAgainstList($value, $values, $ignoreCase = true) {
		return self::_validateValueAgainstList($value, $values, $ignoreCase, true);
	}
	protected function _setPropertyAsObjectOfClass($property, $value, $class) {
		if ($this->_verifyItemAsObjectOfClass($value, $class)) {
			$this->{$property} = $value;
		} else {
			throw new Exception('Unable to set '.$property.'. Supplied value is not an instance of '.$class.'.');
		}
		return $this;
	}
	protected function _verifyItemAsObjectOfClass($value, $class) {
		if ($value instanceof $class) {
			return true;
		}
		return false;
	}
	protected function _getPropertyAsDate($property, $format = 'm/d/Y', $emptyValue = '00/00/0000') {
		if (is_int($this->{$property})) {
			$time = $this->{$property};
		} else {
			$time = strtotime($this->{$property});
		}
		if ($time == null) {
			return $emptyValue;
		}
		return date($format, $time);
	}
	protected function _getPropertyAsDateOrTimestampByFormat($property, $format = 'm/d/Y', $emptyValue = '00/00/0000', $useRelativeDates = false) {
		if (is_int($this->{$property})) {
			$time = $this->{$property};
		} else {
			$time = strtotime($this->{$property});
		}
		if ($format != null) {
			if ($time == null) {
				return $emptyValue;
			}
			if ($useRelativeDates) {
				return $this->timeAsRelativeDateWithFormat($time, $format);
			}
			return date($format, $time);
		}
		return $time;
	}
	protected function _getPropertyAsDatetime($property, $format = 'Y-m-d H:i:s') {
		return $this->_getPropertyAsDate($property, $format);
	}
	protected function _setCodedProperty($property, $value, array $acceptables, $defaultValue = null) {
		// look for this value as an exact match
		if (isset($acceptables[$value])) {
			$this->{$property} = $value;
			return $this;
		}
		// search through the acceptable values to find a match
		$value = strtolower($value);
		$this->{$property} = $defaultValue;
		foreach ($acceptables as $code => $acceptable) {
			if ($value == strtolower($code)) {
				$this->{$property} = $code;
				return $this;
			} else if (is_array($acceptable)) {
				foreach ($acceptable as $option) {
					if (is_bool($option)) {
						if ($value === $option) {
							$this->{$property} = $code;
							return $this;
						}
					} else if ($value == strtolower($option)) {
						$this->{$property} = $code;
						return $this;
					}
				}
			} else if ($value == strtolower($acceptable)) {
				$this->{$property} = $code;
				return $this;
			}
		}
		return $this;
	}
	protected function _getCodedProperty($property, array $codeLabels, $returnCode = false) {
		if ($returnCode == false) {
			if (($label = $this->_arrayItemForKey($codeLabels, $this->{$property})) !== null) {
				return $label;
			}
		}
		return $this->{$property};
	}
// ================== 
// !Static Methods   
// ================== 
	public static function valueAsBoolean($value) {
		return ELSWAK_Boolean::valueAsBoolean($value);
	}
	public static function acceptableTrueValues() {
		return ELSWAK_Boolean::acceptableTrueValues();
	}
	public static function acceptableFalseValues() {
		return ELSWAK_Boolean::acceptableFalseValues();
	}
	public static function valueAsNullBoolean($value) {
		return ELSWAK_NullBoolean::valueAsNullBoolean($value);
	}
	public static function acceptableNullValues() {
		return ELSWAK_NullBoolean::acceptableNullValues();
	}
	public static function makeYearValue($value) {
		$value = abs(intval($value));
		if ($value < 100) {
			if ($value + 2000 < date('Y') + 25) {
				$value += 2000;
			} else {
				$value += 1900;
			}
		}
		return $value;
	}
	public static function timeAsRelativeDateWithFormat($time, $format = 'm/d/y') {
		$today = strtotime(date('Y-m-d'));
		$yesterday = $today - 86400;
		if ($time >= $yesterday) {
			if ($time >= $today) {
				return 'Today';
			}
			return 'Yesterday';
		}
		return date($format, $time);
	}
}
