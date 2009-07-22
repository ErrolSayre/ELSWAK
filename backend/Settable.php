<?php
/*
	ELSWebAppKit Settable
	
	This class is designed to allow PHP classes to behave as more strictly defined models. It prevents properties and methods from being dynamically added to the object, allows defined protected methods to be used without explicitly writing accessor methods, and still allows protected (and private) methods by coupling a protected property with protected accessor methods.
	In addition to the protection of variables, this class allows accessor methods to be accessed as though they are properties providing a seamless interface to the class for properties which require validation or other actions to be performed when being set or retrieved.
*/
class ELSWebAppKit_Settable {
	public function __set($property, $value) {
		// determine if this property can be set or not
		$method = 'set'.$property;
		if (ELSWebAppKit_Settable_Model_Helper::methodExistsForClass($method, $this)) {
			// the property has a public setter method, set the value using the method
			call_user_func(array($this, $method), $value);
		} else if (method_exists($this, $method)) {
			// the property has a protected setter method, protect the property
			throw new Exception('Unable to set property "'.$property.'". Property is protected and has no publically accessible setter method.');
		} else if (property_exists($this, $property)) {
			// the property has no setter method, set the value directly
			$this->{$property} = $value;
		} else {
			// the property is not defined in the class, protect the class definition
			throw new Exception('Unable to set property "'.$property.'". Property is not defined within the class.');
		}
		return $this;
	}
	public function __get($property) {
		// search for getter methods that include the "get" prefix or not.
		$method = 'get'.$property;
		// determine if this property can be accessed
		if (ELSWebAppKit_Settable_Model_Helper::methodExistsForClass($method, $this)) {
			// the property has a public getter method, return the value using the method
			return call_user_func(array($this, $method));
		} else if (method_exists($this, $method)) {
			// the property has a protected getter method, protect the property
			throw new Exception('Unable to get property "'.$property.'". Property is protected and has no publically accessible getter method.');
		} else if (ELSWebAppKit_Settable_Model_Helper::methodExistsForClass($property, $this)) {
			// the property has a public getter method, return the value using the method
			return call_user_func(array($this, $property));
		} else if (method_exists($this, $property)) {
			// the property has a protected getter method, protect the property
			throw new Exception('Unable to get property "'.$property.'". Property is protected and has no publically accessible getter method.');
		} else if (property_exists($this, $property)) {
			// the property is has no getter method, return the value directly
			return $this->{$property};
		} else {
			// the property is not defined in the class, protect the class definition
			throw new Exception('Unable to get property "'.$property.'". Property is not defined within the class.');
		}
		return $this;
	}
	public function __call($method, $arguments) {
		// search for a property that matches the method name
		// determine if this method is a protected internal method
		if (method_exists($this, $method)) {
			// the method exists but is inaccessible, protect it
			throw new Exception('Unable to call method "'.$method.'". Method is protected.');
		}
		// determine if the method name includes "set" or "get"
		if ((stripos($method, 'set') === 0)) {
			return $this->__set(strtolower(substr($method, 3, 1)).substr($method, 4), $arguments[0]);
		}
		if ((stripos($method, 'get') === 0)) {
			return $this->__get(strtolower(substr($method, 3, 1)).substr($method, 4));
		}
		
		// look for this method as a property
		if (count($arguments) == 1) {
			return $this->__set($method, $arguments[0]);
		}
		return $this->__get($method);
	}
	public function _import($import) {
		if (is_array($import) || is_object($import)) {
			foreach ($import as $property => $value) {
				try { $this->__set($property, $value); } catch (Exception $e) {}
			}
		}
		return $this;
	}
	public function _export() {
		$export = array();
		$keys = array_keys(get_object_vars($this));
		foreach ($keys as $property) {
			try { $export[$property] = $this->__get($property); } catch (Exception $e) {}
		}
		return $export;
	}
/*
	public static function _factory($import) {
		echo self::_class();
	}
	public static function _class() {
		return get_called_class();
	}
*/
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
	protected function _addArrayPropertyItem($property, $value) {
		// validate the value if applicable
		if (method_exists($this, '_verify'.$property.'Item')) {
			if (!call_user_func(array($this, '_verify'.$property.'Item'), $value)) {
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
		if (method_exists($this, '_verify'.$property.'Key')) {
			if (!call_user_func(array($this, '_verify'.$property.'Key'), $key)) {
				throw new Exception('Unable to set '.$property.' for key “'.$key.'”. Supplied key does not match accepted keys list.');
			}
		}
		if (isset($this->{$property}[$key])) {
			if (method_exists($this, '_verify'.$property.'Item')) {
				if (call_user_func(array($this, '_verify'.$property.'Item'), $this->{$property}[$key])) {
					return true;
				}
			}
			return empty($this->{$property}[$key]);
		}
		return false;
	}
	protected function _setArrayPropertyItemForKey($property, $key, $value) {
		// validate the key if applicable
		if (method_exists($this, '_verify'.$property.'Key')) {
			if (!call_user_func(array($this, '_verify'.$property.'Key'), $key)) {
				throw new Exception('Unable to set '.$property.' for key “'.$key.'”. Supplied key does not match accepted keys list.');
			}
		}
		// validate the value if applicable
		if (method_exists($this, '_verify'.$property.'Item')) {
			if (!call_user_func(array($this, '_verify'.$property.'Item'), $value)) {
				throw new Exception('Unable to set '.$property.' for key “'.$key.'”. Provided value is invalid.');
			}
		}
		$this->{$property}[$key] = $value;
		return $this;
	}
	protected function _removeArrayPropertyItemForKey($property, $key) {
		if (!empty($this->{$property}[$key])) {
			array_splice($this->{$property}, $key, 1);
		}
		return $this;
	}
	protected function _arrayPropertyKeys($property) {
		return array_keys($this->{$property});
	}
	protected function _arrayPropertyCount($property) {
		return count($this->{$property});
	}
	protected function _verifyArrayPropertyKey($property, $key) {
		if (method_exists($this, '_list'.$property.'Keys')) {
			$keys = call_user_func(array($this, '_list'.$property.'Keys'));
			if (in_array($key, $keys)) {
				return true;
			}
		}
		return false;
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
		if (is_numeric($value)) {
			if ($value > 0) {
				$value = true;
			} else {
				$value = false;
			}
		}
		if (is_string($value)) {
			$value = strtolower($value);
			if (($value == 'yes') ||
				($value == 'y') ||
				($value == 'true')
			) {
				$value = true;
			} else {
				$value = false;
			}
		}
		
		if ($value) {
			$this->{$property} = true;
		} else {
			$this->{$property} = false;
		}
		return $this;
	}
	protected function _setPropertyAsTimestamp($property, $value) {
		if (is_numeric($value)) {
			$this->{$property} = intval($value);
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
	protected function _setPropertyAsEnumeratedValue($property, $value, $values, $ignoreCase = true) {
		if ($ignoreCase)
			$value = strtolower($value);
		foreach ($values as $validKey => $validValue) {
			$compareValue = $validValue;
			if ($ignoreCase)
				$compareValue = strtolower($compareValue);
			if ($value == $compareValue) {
				$this->{$property} = $validValue;
				return $this;
			} else if (!is_numeric($validKey)) {
				// try to compare the value against the key
				$compareValue = $validKey;
				if ($ignoreCase)
					$compareValue = strtolower($compareValue);
				if ($value == $compareValue) {
					$this->{$property} = $validValue;
					return $this;
				}
			}
		}
		throw new Exception('Unable to set '.$property.'. Supplied value does not match accepted values list.');
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
	protected function _getPropertyAsDate($property, $format = 'm/d/Y') {
		if (is_int($this->{$property})) {
			return date($format, $this->{$property});
		}
		return date($format, strtotime($this->{$property}));
	}
	protected function _getPropertyAsDateOrTimestampByFormat($property, $format = 'm/d/Y', $emptyValue = '00/00/0000') {
		if (is_int($this->{$property})) {
			$time = $this->{$property};
		} else {
			$time = strtotime($this->{$property});
		}
		
		if ($format != null) {
			if ($time < 1) {
				return $emptyValue;
			}
			return date($format, $time);
		}
		return $time;
	}
	protected function _getPropertyAsDatetime($property, $format = 'Y-m-d H:i:s') {
		return $this->_getPropertyAsDate($property, $format);
	}
}
class ELSWebAppKit_Settable_Model_Helper {
	public static function methodsForClass($class) {
		return get_class_methods($class);
	}
	public static function methodExistsForClass($method, $class) {
		$method = strtolower($method);
		$methods = self::methodsForClass($class);
		foreach ($methods as $name) {
			if (strtolower($name) == $method) {
				return true;
			}
		}
		return false;
	}
}
