<?php
/*
ELSWAK Model
	
The Model base class seeks to provide two main features:
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

Properties
In order to provide a richer model this class utilizes the following methods to provide metadata about properties.
	set[Property] - allows you to override automatic value setters
	get[Property] - allows you to override automatic value getters
	[property] - synonym of get[Property]
	propertyTypeFor[Property] - see below
	acceptableValuesFor[Property] - for properties denoted as enumerated or coded; you should return a list of acceptable values for automatic filtering
	acceptableKeysFor[Property] - for properties denoted as lists; you should return a list of acceptable keys for automatic filtering
	valueFilterMethodFor[Property] - allows you to override value filtering
	keyFilterMethodFor[Property] - for properties denoted as lists; you should return a key or null

Item Types
You may give the model hints as to how to treat your property by implementing a propertyTypeFor[Property] method and returning the class constant for your type. Specifying a type allows the model to provide addtional "magic methods" for getting/setting your property properly. Every property that defines a type will receive automatic filtering based on that type. You may override this filtering by implementing the filterMethodFor[Property] method to return the name of another method or a function reference compatible with call_user_func_array. See below.

Date Properties
All date/time property types are stored as a unix timestamp (float value to support microseconds). The return values for date property accessors is determined by the type, however additional arguments can be provided for easy formatting and casting of the values at access. See below.

List Properties
List properties (sometimes referred to as a "list of things") are arrays that contain items that match one of the item types. Once indicated as a list property, the __call method allows several standard operations to be performed upon the list. The operations assume the "thing" from the list of things as a singular noun and the property as a plural of the same noun. For items which pluralize in an abnormal manner (not simply adding an s) you may implement the propertyFor[Thing] method to indicate the appropriate property. (Wether the list is a numeric or associative array, the index is always referred to as "key" for consistent nomenclature.)
	get[Thing]ForKey($key) - returns an item in the list corresponding to the supplied key of if the key is not set will return null
		- you may implement nullValueFor[Thing] for custom behavior
	[thing]ForKey($key) - synonym of get[Thing]ForKey
	set[Thing]ForKey($item, $key) - inserts/replaces the item at a particular slot in the list and returns $this
		- if item provided is null the specified item is removed
	add[Thing]($item = null) - inserts the item at the end of an indexed list and returns $this
		- if no item is provided and the itemTypeFor[Property] method is implemented, will create the appropriate new item
	new[Thing]($item = null) - inserts the item at the end of an indexed list and returns the item for additional manipulation
		- if no item is provided and the itemTypeFor[Property] method is implemented, will create the appropriate new item
	new[Thing]ForKey($item, $key) - inserts/replaces the item at a particular slot in the list and returns the item for additional manipulation
		- if item provided is null and the itemTypeFor[Property] method is implemented, will create the appropriate new item
	remove[Thing]($item) - locates the item in the list and removes it
	remove[Thing]ForKey($key) - synonym for set[Thing]ForKey(null, $key)
*/
class ELSWAK_Model {
	private static $_properties;
	
// =================== 
// !Class Constants   
// =================== 
// ==================== 
// !	Setter Types   
// ==================== 
	const SETTER_TYPE_PROTECT_CLASS = -2;
	const SETTER_TYPE_PROTECT_PROPERTY = -1;
	const SETTER_TYPE_UNKNOWN = 0;
	const SETTER_TYPE_DIRECT = 1;
	const SETTER_TYPE_METHOD = 2;
	const SETTER_TYPE_AUTOMATIC = 3;
	const SETTER_TYPE_FILTERED = 4;
// ================== 
// !	Item Types   
// ================== 
	const ITEM_TYPE_NO_TYPE = 0;
	
	const ITEM_TYPE_STRING = 10;
	const ITEM_TYPE_ENUMERATED_VALUE = 11;
	const ITEM_TYPE_CODED_VALUE = 12;
	
	const ITEM_TYPE_NUMERIC = 20;
	const ITEM_TYPE_INTEGER = 21;
	const ITEM_TYPE_POSITIVE_INTEGER = 22;
	const ITEM_TYPE_FLOAT = 23;
	const ITEM_TYPE_TIMESTAMP = 24;
	
	const ITEM_TYPE_BOOLEAN = 30;
	const ITEM_TYPE_NULLBOOLEAN = 31;
	
	const ITEM_TYPE_OBJECT = 40;
	
	const ITEM_TYPE_ARRAY = 50;
	const ITEM_TYPE_INDEXED_ARRAY = 51;
	const ITEM_TYPE_KEYED_ARRAY = 52;
// ================= 
// !Magic Methods   
// ================= 
	public function __set($property, $value) {
		// determine if this class has been examined before
		$className = get_class($this);
		if (!isset(self::$_setters[$className])) {
			self::$_setters[$className] = array();
		}
		if (!isset(self::$_types[$className])) {
			self::$_types[$className] = array();
		}
		if (!isset(self::$_filters[$className])) {
			self::$_filters[$className] = array();
		}
		
		// determine if this property has been examined before
		if (!isset(self::$_setters[$className][$property])) {
			// determine if this property can be set or not
			$method = 'set'.$property;
			if (ELSWAK_Model_Helper::methodExistsForClass($method, $this)) {
				// the property has a public setter method, set the value using the method
				self::$_setters[$className][$property] = self::SETTER_TYPE_METHOD;
			} else if (method_exists($this, $method)) {
				// the property has a protected setter method, protect the property
				self::$_setters[$className][$property] = self::SETTER_TYPE_PROTECT_PROPERTY;
			} else if (property_exists($this, $property)) {
				// the property has no setter method, determine how to set the value automatically
				// determine the type
				$type = self::PROPERTY_TYPE_NO_TYPE;
				if (method_exists($this, 'propertyTypeFor'.$property)) {
					$type = $this->{'propertyTypeFor'.$property}();
				}
				// determine the filter
				$filter = false;
				if (method_exists($this, 'filterValueFor'.$property)) {
					$filter = true;
				}
				
				// default to setting the value directly
				self::$_setters[$className][$property] = self::SETTER_TYPE_DIRECT;
				
				// determine if this property has a defined type
				if (method_exists($this, 'propertyTypeFor'.$property)) {
					// default to automatic
					self::$_setters[$className][$property] = self::SETTER_TYPE_AUTOMATIC;
					// based on the type, determine if there are any overrides for how this value should be processed
					if ($type == self::PROPERTY_TYPE_NO_TYPE) {
						// since this is an explicitly no type property, set the value directly
						
					}
				// determine if this property has a defined filter
				} else if (method_exists($this, 'filterValueFor'.$property)) {
					self::$_setters[$className][$property] = self::SETTER_TYPE_FILTERED;
				}
			} else {
				// the property is not defined in the class, protect the class definition
				self::$_setters[$className][$property] = self::SETTER_TYPE_PROTECT_CLASS;
			}
		}
		
		// perform the determined operation
		if (self::$_setters[$className][$property] == self::SETTER_TYPE_DIRECT) {
			$this->{$property} = $value;
		} else if (self::$_setters[$className][$property] == self::SETTER_TYPE_METHOD) {
			$this->{'set'.$property}($value);
		} else if (self::$_setters[$className][$property] == self::SETTER_TYPE_FILTERED) {
			$this->{$property} = $this->{'filterValueFor'.$property}($value);
		} else if (self::$_setters[$className][$property] == self::SETTER_TYPE_AUTOMATIC) {
			$this->setPropertyAccordingToType($property, $value, $this->{'propertyTypeFor'.$property});
		} else if (self::$_setters[$className][$property] == self::SETTER_TYPE_PROTECT_PROPERTY) {
			throw new Exception('Unable to set property "'.$property.'". Property is protected and has no publically accessible setter method.');
		} else if (self::$_setters[$className][$property] == self::SETTER_TYPE_PROTECT_CLASS) {
			throw new Exception('Unable to set property "'.$property.'". Property is not defined within the class "'.$className.'".');
		} else {
			throw new Exception('Unable to set property "'.$property.'". Property could not be evaluated for action.');
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
			if (ELSWAK_Model_Helper::methodExistsForClass($method, $this)) {
				// the property has a public getter method, return the value using the method
				self::$_getters[$className][$property] = 2;
			} else if (method_exists($this, $method)) {
				// the property has a protected getter method, protect the property
				self::$_getters[$className][$property] = -1;
			} else if (ELSWAK_Model_Helper::methodExistsForClass($property, $this)) {
				// the property has a public getter method named as the property, return the value using the method
				self::$_getters[$className][$property] = 3;
			} else if (method_exists($this, $property)) {
				// the property has a protected getter method named as the property, protect the property
				self::$_getters[$className][$property] = -1;
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
			throw new Exception('Unable to get property "'.$property.'". Property is protected and has no publically accessible getter method.');
		} else {
			throw new Exception('Unable to get property "'.$property.'". Property is not defined within the class "'.$className.'".');
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
		if (!isset(self::$_callers[$className][$method]) || !is_array(self::$_callers[$className][$method])) {
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
			throw new Exception('Unable to call method "'.$method.'". Method is protected.');
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
// ================== 
// !Model Defaults   
// ================== 
	public function __construct($import = null) {
/*
	This default constructor allows simple creation of a new model by passing in another object or an associative array for import.
*/
		$this->import($import);
	}
	public function __toString() {
/*
	The default toString method uses the describe method to produce a machine and human readable representation of the object akin to Cocoa.
*/
		return $this->describe();
	}
	public function describe($padding = '', $json = false) {
		$values = array();
		$keys = array_keys(get_object_vars($this));
		foreach ($keys as $property) {
			try {
				$value = $this->__get($property);
				if ($value instanceof ELSWAK_Settable) {
					$value = $value->describe($padding.TAB, $json);
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
	public function toJSON($padding = '') {
		return $this->describe($padding, true);
	}
	public function import($import) {
		if (is_array($import) || is_object($import)) {
			foreach ($import as $property => $value) {
				try { $this->__set($property, $value); } catch (Exception $e) {}
			}
		}
		return $this;
	}
	public function export() {
		$export = array();
		$keys = array_keys(get_object_vars($this));
		foreach ($keys as $property) {
			try { $export[$property] = $this->__get($property); } catch (Exception $e) {}
		}
		return $export;
	}
// ============================ 
// !General Property Methods   
// ============================ 
	protected function setterTypeForClassAndProperty($class, $property) {
		if (isset(self::$_properties[$class])) {
			if (isset(self::$_properties[$class][$property])) {
				if (isset(self::$_properties[$class][$property]['setter type'])) {
					return self::$_properties[$class][$property]['setter type'];
				}
			}
		}
		return self::SETTER_TYPE_UNKNOWN;
	}
	protected function setSetterTypeForClassAndProperty($setterType, $class, $property) {
		if (!isset(self::$_properties[$class])) {
			self::$_properties[$class] = array();
		}
		if (!isset(self::$_properties[$class])) {
	}
	protected function setPropertyAccordingToType($property, $value, $type) {
		if ($type == )
	}
}
class ELSWAK_Model_Helper {
/*
	This class provides a mechanism by which the model can identify its methods which are visible to other classes.
*/
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
