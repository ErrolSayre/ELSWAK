<?php
/*
ELSWebAppKit Settable
	
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
class ELSWebAppKit_Settable {
	private static $_getters;
	private static $_setters;
	private static $_callers;
	
	public function __set($property, $value) {
		// determine if this property has been examined before
		if (!isset(self::$_setters[$property])) {
			// determine if this property can be set or not
			$method = 'set'.$property;
			if (ELSWebAppKit_Settable_Model_Helper::methodExistsForClass($method, $this)) {
				// the property has a public setter method, set the value using the method
				self::$_setters[$property] = 2;
			} else if (method_exists($this, $method)) {
				// the property has a protected setter method, protect the property
				self::$_setters[$property] = -1;
			} else if (property_exists($this, $property)) {
				// the property has no setter method, set the value directly
				self::$_setters[$property] = 1;
			} else {
				// the property is not defined in the class, protect the class definition
				self::$_setters[$property] = -2;
			}
		}
		
		// perform the determined operation
		if (self::$_setters[$property] == 1) {
			$this->{$property} = $value;
		} else if (self::$_setters[$property] == 2) {
			$this->{'set'.$property}($value);
		} else if (self::$_setters[$property] == -1) {
			throw new Exception('Unable to set property "'.$property.'". Property is protected and has no publically accessible setter method.');
		} else {
			throw new Exception('Unable to set property "'.$property.'". Property is not defined within the class "'.get_class($this).'".');
		}
		return $this;
	}
	public function __get($property) {
		// determine if this property has been examined before
		if (!isset(self::$_getters[$property])) {
			// determine if this property can be accessed
			// search for getter methods that include the "get" prefix or not.
			$method = 'get'.$property;
			if (ELSWebAppKit_Settable_Model_Helper::methodExistsForClass($method, $this)) {
				// the property has a public getter method, return the value using the method
				self::$_getters[$property] = 2;
			} else if (method_exists($this, $method)) {
				// the property has a protected getter method, protect the property
				self::$_getters[$property] = -1;
			} else if (ELSWebAppKit_Settable_Model_Helper::methodExistsForClass($property, $this)) {
				// the property has a public getter method named as the property, return the value using the method
				self::$_getters[$property] = 3;
			} else if (method_exists($this, $property)) {
				// the property has a protected getter method named as the property, protect the property
				self::$_getters[$property] = -1;
			} else if (property_exists($this, $property)) {
				// the property is has no getter method, return the value directly
				self::$_getters[$property] = 1;
			} else {
				// the property is not defined in the class, protect the class definition
				self::$_getters[$property] = -2;
			}
		}
		
		// perform the determined operation
		if (self::$_getters[$property] == 1) {
			return $this->{$property};
		} else if (self::$_getters[$property] == 2) {
			return $this->{'get'.$property}();
		} else if (self::$_getters[$property] == 3) {
			return $this->{$property}();
		} else if (self::$_getters[$property] == -1) {
			throw new Exception('Unable to get property "'.$property.'". Property is protected and has no publically accessible getter method.');
		} else {
			throw new Exception('Unable to get property "'.$property.'". Property is not defined within the class "'.get_class($this).'".');
		}
		return $this;
	}
	public function __call($method, $arguments) {
		// determine if this method has been examined before
		if (!isset(self::$_callers[$method]) || !is_array(self::$_callers[$method])) {
			// set the default
			self::$_callers[$method] = array(
				'type' => 0,
				'property' => null
			);
			
			// determine if this method is a protected internal method or a property
			if (method_exists($this, $method)) {
				// the method exists but is inaccessible (otherwise the __call method would not have been called)
				// protect the method
				self::$_callers[$method]['type'] = -1;
			} else if ((stripos($method, 'set') === 0)) {
				self::$_callers[$method]['type'] = 1;
				self::$_callers[$method]['property'] = strtolower(substr($method, 3, 1)).substr($method, 4);
			} else if ((stripos($method, 'get') === 0)) {
				self::$_callers[$method]['type'] = 1;
				self::$_callers[$method]['property'] = strtolower(substr($method, 3, 1)).substr($method, 4);
			} else {
				self::$_callers[$method]['type'] = 1;
				self::$_callers[$method]['property'] = $method;
			}
		}
		
		// perform the determined operation
		if (self::$_callers[$method]['type'] == -1) {
			throw new Exception('Unable to call method "'.$method.'". Method is protected.');
		} else if (
			(self::$_callers[$method]['type'] == 1) &&
			(count($arguments) == 1)
		) {
			// attempt to set the property with the provided argument, allowing the __set method to throw any appropriate exceptions
			return $this->__set(self::$_callers[$method]['property'], $arguments[0]);
		}
		// since no matching method exists and there is not an appropriate number of arguments for a set operation, attempt to get the property, allowing the __get method to throw any appropriate exceptions
		return $this->__get(self::$_callers[$method]['property']);
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
		if (method_exists($this, '_verify'.$property.'Key')) {
			if (!$this->{'_verify'.$property.'Key'}($key)) {
				throw new Exception('Unable to set '.$property.' for key “'.$key.'”. Supplied key does not match accepted keys list.');
			}
		}
		if (isset($this->{$property}[$key])) {
			if (method_exists($this, '_verify'.$property.'Item')) {
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
		if (method_exists($this, '_verify'.$property.'Key')) {
			if (!$this->{'_verify'.$property.'Key'}($key)) {
				throw new Exception('Unable to set '.$property.' for key “'.$key.'”. Supplied key does not match accepted keys list.');
			}
		}
		// validate the value if applicable
		if (method_exists($this, '_verify'.$property.'Item')) {
			if (!$this->{'_verify'.$property.'Item'}($value)) {
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
			$keys = $this->{'_list'.$property.'Keys'}();
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
	protected function _setPropertyAsNullBoolean($property, $value) {
		if (is_string($value)) {
			$value = strtolower($value);
			if (($value == 'pending') ||
				($value == 'null') ||
				($value == 'p')
			) {
				$value = null;
			}
		}
		if ($value === null) {
			$this->{$property} = null;
			return $this;
		}
		return $this->_setPropertyAsBoolean($property, $value);
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
