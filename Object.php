<?php
//!Related Exception Classes
class ELSWAK_Object_Exception extends ELSWAK_Exception {}
class ELSWAK_Object_NonexistentProperty_Exception extends ELSWAK_Object_Exception {}
class ELSWAK_Object_ProtectedProperty_Exception extends ELSWAK_Object_Exception {}
class ELSWAK_Object_ProtectedMethod_Exception extends ELSWAK_Object_Exception {}

// Place a stub for backward compatibility with PHP < 5.4.0
if (!interface_exists('JsonSerializable')) {
	require_once 'JsonSerializeableInterface.php';
}

/**
 * Provides a replacement for ELSWAK Settable as a more basic building
 * block for building more structured classes with default getters and
 * setters.
 */
abstract class ELSWAK_Object
	implements JsonSerializable {
	
	private static $_getters = array();
	private static $_setters = array();
	private static $_callers = array();
	private static $_methods = array();
	
	public function __construct($import = null) {
		if ($import) {
			$this->_import($import);
		}
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
			try {
				$export[$property] = $this->__get($property);
			} catch (Exception $e) {}
		}
		return $export;
	}
	public function jsonSerialize() {
		return $this->_export();
	}
	public function __toString() {
		return json_encode($this);
	}
	
	
	
//!Magic Method Default Getter/Setter methods
	/**
	 * Utilize the __set "magic" method to provide all properties a default setter.
	 */
	public function __set($property, $value) {
		// determine if this class has been examined before
		$className = get_class($this);
		if (!isset(self::$_setters[$className])) {
			self::$_setters[$className] = array();
		}
		$method = 'set'.ucfirst($property);
		
		// determine if this property has been examined before
		if (!isset(self::$_setters[$className][$property])) {
			// determine if this property can be set or not
			if (ELSWAK_Object_Model_Helper::methodExistsForClass($method, $this)) {
				// the property has a public setter method, set the value using the method
				self::$_setters[$className][$property] = 2;
				$this->_registerMethod($method);
			} else if (method_exists($this, $method)) {
				// the property has a protected setter method, protect the property
				self::$_setters[$className][$property] = -1;
				$this->_registerMethod($method);
			} else if (property_exists($this, $property)) {
				// the property has no setter method, set the value directly
				self::$_setters[$className][$property] = 1;
			} else {
				// the property is not defined in the class, protect the class definition
				self::$_setters[$className][$property] = -2;
			}
		}
		
		// perform the determined operation
		if (self::$_setters[$className][$property] == 1) {
			$this->{$property} = $value;
		} else if (self::$_setters[$className][$property] == 2) {
			$this->{$method}($value);
		} else if (self::$_setters[$className][$property] == -1) {
			throw new ELSWAK_Object_ProtectedProperty_Exception('Unable to set property "'.$property.'". Property is protected and has no publicly accessible setter method.');
		} else {
			throw new ELSWAK_Object_NonexistentProperty_Exception('Unable to set property "'.$property.'". Property is not defined within the class "'.$className.'".');
		}
		return $this;
	}
	public function __get($property) {
		// determine if this class has been examined before
		$className = get_class($this);
		if (!isset(self::$_getters[$className])) {
			self::$_getters[$className] = array();
		}
		
		// determine if this property has been examined before
		if (!isset(self::$_getters[$className][$property])) {
			// determine if this property can be accessed
			// search for getter methods that include the "get" prefix or not.
			$method = 'get'.$property;
			if (ELSWAK_Object_Model_Helper::methodExistsForClass($method, $this)) {
				// the property has a public getter method, return the value using the method
				self::$_getters[$className][$property] = 2;
				$this->_registerMethod($method);
			} else if (method_exists($this, $method)) {
				// the property has a protected getter method, protect the property
				self::$_getters[$className][$property] = -1;
				$this->_registerMethod($method);
			} else if (ELSWAK_Object_Model_Helper::methodExistsForClass($property, $this)) {
				// the property has a public getter method named as the property, return the value using the method
				self::$_getters[$className][$property] = 3;
				$this->_registerMethod($method);
			} else if (method_exists($this, $property)) {
				// the property has a protected getter method named as the property, protect the property
				self::$_getters[$className][$property] = -1;
				$this->_registerMethod($method);
			} else if (property_exists($this, $property)) {
				// the property is has no getter method, return the value directly
				self::$_getters[$className][$property] = 1;
			} else {
				// the property is not defined in the class, protect the class definition
				self::$_getters[$className][$property] = -2;
			}
		}
		
		// perform the determined operation
		if (self::$_getters[$className][$property] == 1) {
			return $this->{$property};
		} else if (self::$_getters[$className][$property] == 2) {
			return $this->{'get'.$property}();
		} else if (self::$_getters[$className][$property] == 3) {
			return $this->{$property}();
		} else if (self::$_getters[$className][$property] == -1) {
			throw new ELSWAK_Object_ProtectedProperty_Exception('Unable to get property "'.$property.'". Property is protected and has no publically accessible getter method.');
		} else {
			throw new ELSWAK_Object_NonexistentProperty_Exception('Unable to get property "'.$property.'". Property is not defined within the class "'.$className.'".');
		}
		return $this;
	}
	public function __call($method, $arguments) {
		// determine if this class has been examined before
		$className = get_class($this);
		if (!isset(self::$_callers[$className])) {
			self::$_callers[$className] = array();
		}
		
		// determine if this method has been examined before
		if (!array_key_exists($method, self::$_callers[$className]) || !is_array(self::$_callers[$className][$method])) {
			// set the default
			self::$_callers[$className][$method] = array(
				'type' => 0,
				'property' => null
			);
			
			// determine if this method is a protected internal method or a property
			if (method_exists($this, $method)) {
				// the method exists but is inaccessible (otherwise the __call method would not have been called)
				// protect the method
				self::$_callers[$className][$method]['type'] = -1;
				$this->_registerMethod($method);
			} else if ((stripos($method, 'set') === 0)) {
				self::$_callers[$className][$method]['type'] = 1;
				self::$_callers[$className][$method]['property'] = strtolower(substr($method, 3, 1)).substr($method, 4);
			} else if ((stripos($method, 'get') === 0)) {
				self::$_callers[$className][$method]['type'] = 1;
				self::$_callers[$className][$method]['property'] = strtolower(substr($method, 3, 1)).substr($method, 4);
			} else {
				self::$_callers[$className][$method]['type'] = 1;
				self::$_callers[$className][$method]['property'] = $method;
			}
		}
		
		// perform the determined operation
		if (self::$_callers[$className][$method]['type'] == -1) {
			throw new ELSWAK_Object_ProtectedMethod_Exception('Unable to call method "'.$method.'". Method is protected.');
		} else if (
			(self::$_callers[$className][$method]['type'] == 1) &&
			(count($arguments) == 1)
		) {
			// attempt to set the property with the provided argument, allowing the __set method to throw any appropriate exceptions
			return $this->__set(self::$_callers[$className][$method]['property'], $arguments[0]);
		}
		// since no matching method exists and there is not an appropriate number of arguments for a set operation, attempt to get the property, allowing the __get method to throw any appropriate exceptions
		return $this->__get(self::$_callers[$className][$method]['property']);
	}
//!Magic Method assistance methods
	protected function _registerMethod($method) {
		$className = get_class($this);
		if (!array_key_exists($className, self::$_methods)) {
			self::$_methods[$className] = array();
		}
		self::$_methods[$className][strtolower($method)] = true;
		return $this;
	}
	public function _methodExists($method) {
		$className = get_class($this);
		$compareMethod = strtolower($method);
		// ensure the appropriate slot is available
		if (!array_key_exists($className, self::$_methods)) {
			self::$_methods[$className] = array();
		}
		// check if this method has been seen before
		if (array_key_exists($compareMethod, self::$_methods[$className])) {
			if (self::$_methods[$className][$compareMethod] === true) {
				return true;
			}
		} elseif (method_exists($this, $method)) {
			$this->_registerMethod($method);
			return true;
		}
		return false;
	}
}
/**
 * Provide an external vantage point for determining the visibility of methods.
 */
class ELSWAK_Object_Model_Helper {
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