<?php
/**
 * ELSWAK Object
 *
 * @author Errol Sayre
 */



//!Related Exceptions
/**
 * Generic exception for ELSWAK Objects.
 *
 * @package ELSWAK
 */
class ELSWAK_Object_Exception extends ELSWAK_Exception {}
/**
 * Exception for attempting to access a non-existent ELSWAK Object property.
 *
 * @package ELSWAK
 */
class ELSWAK_Object_NonexistentProperty_Exception extends ELSWAK_Object_Exception {}
/**
 * Exception for attempting to access an ELSWAK Object property protected by non-public accessors.
 *
 * @package ELSWAK
 */
class ELSWAK_Object_ProtectedProperty_Exception extends ELSWAK_Object_Exception {}
/**
 * Exception for attempting to access a non-public method of an ELSWAK Object.
 *
 * @package ELSWAK
 */
class ELSWAK_Object_ProtectedMethod_Exception extends ELSWAK_Object_Exception {}



//!Functionality Stubs
// Place a stub for backward compatibility with PHP < 5.4.0
if (!interface_exists('JsonSerializable')) {
	require_once 'JsonSerializeableInterface.php';
}



//!Class Definition
/**
 * Basic building block for objects with automatic accessors.
 * 
 * Provides a replacement for ELSWAK Settable as a more basic building
 * block for building more structured classes with default getters and
 * setters.
 *
 * @package ELSWAK
 */
abstract class ELSWAK_Object
	implements JsonSerializable, ArrayAccess {



//!Class constants
//!— Comparisons
	/**
	 * Left and right equal
	 * @type integer
	 */
	const COMPARISON_EQUAL = 0;

	/**
	 * Left operand smaller/lesser/earlier
	 *
	 * I define these seemingly duplicate constants to allow the developer
	 * to pick values that make the most sense to him while fitting the
	 * algorithm of the comparison.
	 *
	 * @type integer
	 */
	const COMPARISON_LEFT_LESSER = -1;

	/**
	 * Left operand larger/greater/later
	 * @type integer
	 */
	const COMPARISON_LEFT_GREATER = 1;

	/**
	 * Right operand smaller/lesser/earlier
	 * @type integer
	 */
	const COMPARISON_RIGHT_LESSER = 1;

	/**
	 * Right operand larger/greater/later
	 * @type integer
	 */
	const COMPARISON_RIGHT_GREATER = -1;



//!Static properties
	/**
	 * Memoized getter collection
	 *
	 * Collect all of the discovered and automagic setter methods segregated by class such that the result is only stored (and determined) once per class and is accessible to all instances.
	 */
	private static $_getters = array();

	/**
	 * Memoized setter collection
	 *
	 * Collect all of the discovered and automagic setter methods segregated by class such that the result is only stored (and determined) once per class and is accessible to all instances.
	 */
	private static $_setters = array();

	/**
	 * Memoized caller collection
	 *
	 * Collect all of the discovered and automagic methods segregated by class such that the result is only stored (and determined) once per class and is accessible to all instances.
	 */
	private static $_callers = array();

	/**
	 * Memoized method collection
	 *
	 * Collect all of the discovered methods segregated by class such that the result is only stored (and determined) once per class and is accessible to all instances.
	 */
	private static $_methods = array();



//!Instance methods
	/**
	 * Default the constructor to include property import.
	 *
	 * All objects can natively support importing values from an array or iterable object by key/property. This generic method in-turn means constructors are purely optional to implement.
	 *
	 * @param array|object $import Array or iterable object. 
	 */
	public function __construct($import = null) {
		if ($import) {
			$this->_import($import);
		}
	}
	
	/**
	 * Import values from any iterable collection.
	 *
	 * @param array|object $import Array or iterable object. 
	 *
	 * @return ELSWAK_Object self
	 */
	public function _import($import) {
		if (is_array($import) || is_object($import)) {
			foreach ($import as $property => $value) {
				try { $this->__set($property, $value); } catch (Exception $e) {}
			}
		}
		return $this;
	}
	
	/**
	 * Export properties of this object as an array.
	 *
	 * @return array
	 */
	public function _export() {
		$export = array();
		$keys = array_keys(get_object_vars($this));
		foreach ($keys as $property) {
			if ($this->{$property} instanceof ELSWAK_Object) {
				$export[$property] = $this->{$property}->_export();
			} else {
				try {
					$export[$property] = $this->__get($property);
				} catch (Exception $e) {}
			}
		}
		return $export;
	}



//!JSONSerializable methods
	/**
	 * Provide the JSON encoder with an easy to handle array.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->_export();
	}
	
	/**
	 * Default to JSON representation like Objective-C's describe method.
	 *
	 * @return string
	 */
	public function __toString() {
		return json_encode($this);
	}



//!Object Comparison methods
	/**
	 * Compare two objects
	 *
	 * Since this is a generic method that is intended to be overriden by
	 * subclasses, for now we'll rely upon PHP’s built-in comparison.
	 *
	 * @param mixed $that
	 * @return integer
	 */
	public function compare($that) {
		if ($this == $that) {
			return self::COMPARISON_EQUAL;
		} elseif ($this < $that) {
			return self::COMPARISON_LEFT_LESSER;
		}
		return self::COMPARISON_LEFT_GREATER;
	}

	/**
	 * Short-cut for comparison
	 *
	 * Each of these short-cut methods is designed to operate within the
	 * context of the results of the compare method. This means that
	 * subclasses can override one method and still have these three
	 * shortcuts as long as they maintain the same convention i.e:
	 * - this less than that yields -1
	 * - this equal to that yields 0
	 * - this greater than that yields 1
	 *
	 * @param mixed $that
	 * @return boolean
	 */
	public function isEqualTo($that) {
		if ($this->compare($that) == self::COMPARISON_EQUAL) {
			return true;
		}
		return false;
	}

	/**
	 * Short-cut for comparison
	 *
	 * @param mixed $that
	 * @return boolean
	 */
	public function isLessThan($that) {
		if ($this->compare($that) == self::COMPARISON_LEFT_LESSER) {
			return true;
		}
		return false;
	}

	/**
	 * Short-cut for comparison
	 *
	 * @param mixed $that
	 * @return boolean
	 */
	public function isGreaterThan($that) {
		if ($this->compare($that) == self::COMPARISON_LEFT_GREATER) {
			return true;
		}
		return false;
	}



//!ArrayAccess methods
	/**
	 * Set a property via array notation.
	 * @return mixed|ELSWAK_Object self
	 */
	public function offsetSet($offset, $value) {
		return $this->__set($offset, $value);
	}
	
	/**
	 * Determine if the offset is "gettable" as a real or virtual property.
	 * @return boolean
	 */
	public function offsetExists($offset) {
		try {
			$this->__get($offset);
			return true;
		} catch (ELSWAK_Object_Exception $e) {}
		return false;
	}
	
	/**
	 * Restore the property to a null state.
	 *
	 * This will only work if the property is able to be set to null, which may or may not be allowed based upon the extending class definition.
	 * @return mixed|ELSWAK_Object self
	 */
	public function offsetUnset($offset) {
		try {
			return $this->__set($offset, null);
		} catch (ELSWAK_Object_Exception $e) {}
		return $this;
	}
	
	/**
	 * Get a property via array notation.
	 * @return mixed
	 */
	public function offsetGet($offset) {
		try {
			return $this->__get($offset);
		} catch (ELSWAK_Object_Exception $e) {}
		return null;
	}

	
	
	
//!Magic Method Default Getter/Setter methods
	/**
	 * Utilize the __set "magic" method to provide all properties a default setter.
	 *
	 * @param string $property The property to set.
	 * @param mixed $value The value to set the given property.
	 * @return ELSWAK_Object self
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
			if (ELSWAK_Object_Helper::methodExistsForClass($method, $this)) {
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
	
	/**
	 * Return the appropriate value from a matching property.
	 *
	 * @param string $property The property to get.
	 *
	 * @return mixed
	 */
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
			if (ELSWAK_Object_Helper::methodExistsForClass($method, $this)) {
				// the property has a public getter method, return the value using the method
				self::$_getters[$className][$property] = 2;
				$this->_registerMethod($method);
			} else if (method_exists($this, $method)) {
				// the property has a protected getter method, protect the property
				self::$_getters[$className][$property] = -1;
				$this->_registerMethod($method);
			} else if (ELSWAK_Object_Helper::methodExistsForClass($property, $this)) {
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
	
	/**
	 * Call the matching method or pseudo-method.
	 *
	 * @param string $method The method to call.
	 * @param mixed|array $arguments The arguments to pass to the called method.
	 *
	 * @return mixed|void
	 */
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
	/**
	 * Provide a gentle getter
	 *
	 * Allow the caller to ask for a real or virtual property without
	 * complaining.
	 *
	 * @param string $property
	 * @return mixed|null
	 */
	public function get($property) {
		try {
			return $this->__get($property);
		} catch (ELSWAK_Object_Exception $e) {}
		return null;
	}
	/**
	 * Register a located method.
	 *
	 * When a method is determined to exist, add it to the listing to avoid performing another lookup.
	 *
	 * @param string $method The method to register/memoize.
	 *
	 * @return ELSWAK_Object self
	 */
	protected function _registerMethod($method) {
		$className = get_class($this);
		if (!array_key_exists($className, self::$_methods)) {
			self::$_methods[$className] = array();
		}
		self::$_methods[$className][strtolower($method)] = true;
		return $this;
	}
	
	/**
	 * Determine if a method exists and wether it is publicly accessible.
	 *
	 * @param string $method The method to check.
	 *
	 * @return boolean
	 */
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



//!Related classes
/**
 * Provide an external vantage point for determining the visibility of methods.
 *
 * @package ELSWAK
 */
class ELSWAK_Object_Helper {
	
	/**
	 * Get the methods from the other class visible to this class.
	 *
	 * @param string|object $class The class to examine.
	 *
	 * return array
	 */
	public static function methodsForClass($class) {
		return get_class_methods($class);
	}
	
	/**
	 * Determine if a method exists for a given class.
	 *
	 * @param string $method The method name to check for.
	 * @param string|object $class The class to check within.
	 *
	 * return boolean
	 */
	public static function methodExistsForClass($method, $class) {
		// ensure we're inspecting an object rather than getting the output of __toString()
		if (is_object($class)) {
			$class = get_class($class);
		}
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
