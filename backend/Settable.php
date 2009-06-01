<?php
/*
	ELSWebAppKit Settable
	
	This class is designed to allow PHP classes to behave as more strictly defined models. It prevents properties and methods from being dynamically added to the object, allows defined protected methods to be used without explicitly writing accessor methods, and still allows protected (and private) methods by coupling a protected property with protected accessor methods.
	In addition to the protection of variables, this class allows accessor methods to be accessed as though they are properties providing a seamless interface to the class for properties which require validation or other actions to be performed when being set or retrieved.
*/
class ELSWebAppKit_Settable
{
	public function __set($property, $value)
	{
		// determine if this property can be set or not
		$method = 'set'.$property;
		if (ELSWebAppKit_Settable_Model_Helper::methodExistsForClass($method, $this))
			// the property has a public setter method, set the value using the method
			call_user_func(array($this, $method), $value);
		else if (method_exists($this, $method))
			// the property has a protected setter method, protect the property
			throw new Exception('Unable to set property "'.$property.'". Property is protected and has no publically accessible setter method.');
		else if (property_exists($this, $property))
			// the property has no setter method, set the value directly
			$this->{$property} = $value;
		else
			// the property is not defined in the class, protect the class definition
			throw new Exception('Unable to set property "'.$property.'". Property is not defined within the class.');
		return $this;
	}
	public function __get($property)
	{
		// search for getter methods that include the "get" prefix or not.
		$method = 'get'.$property;
		// determine if this property can be accessed
		if (ELSWebAppKit_Settable_Model_Helper::methodExistsForClass($method, $this))
			// the property has a public getter method, return the value using the method
			return call_user_func(array($this, $method));
		else if (method_exists($this, $method))
			// the property has a protected getter method, protect the property
			throw new Exception('Unable to get property "'.$property.'". Property is protected and has no publically accessible getter method.');
		else if (ELSWebAppKit_Settable_Model_Helper::methodExistsForClass($property, $this))
			// the property has a public getter method, return the value using the method
			return call_user_func(array($this, $property));
		else if (method_exists($this, $property))
			// the property has a protected getter method, protect the property
			throw new Exception('Unable to get property "'.$property.'". Property is protected and has no publically accessible getter method.');
		else if (property_exists($this, $property))
			// the property is has no getter method, return the value directly
			return $this->{$property};
		else
			// the property is not defined in the class, protect the class definition
			throw new Exception('Unable to get property "'.$property.'". Property is not defined within the class.');
		return $this;
	}
	public function __call($method, $arguments)
	{
		// search for a property that matches the method name
		// determine if this method is a protected internal method
		if (method_exists($this, $method))
			// the method exists but is inaccessible, protect it
			throw new Exception('Unable to call method "'.$method.'". Method is protected.');
		
		// determine if the method name includes "set" or "get"
		if ((stripos($method, 'set') === 0))
		{
			return $this->__set(strtolower(substr($method, 3, 1)).substr($method, 4), $arguments[0]);
		}
		if ((stripos($method, 'get') === 0))
			return $this->__get(strtolower(substr($method, 3, 1)).substr($method, 4));
		
		// look for this method as a property
		if (count($arguments) == 1)
			return $this->__set($method, $arguments[0]);
		return $this->__get($method);
	}
	public function _import($import)
	{
		if (is_array($import) || is_object($import))
		foreach ($import as $property => $value)
			try { $this->__set($property, $value); } catch (Exception $e) {}
		return $this;
	}
	public function _export()
	{
		$export = array();
		$keys = array_keys(get_object_vars($this));
		foreach ($keys as $property)
			try { $export[$property] = $this->__get($property); } catch (Exception $e) {}
		return $export;
	}
/*
	public static function _factory($import)
	{
		echo self::_class();
	}
	public static function _class()
	{
		return get_called_class();
	}
*/
	protected function _setArrayProperty($property, $value)
	{
		$this->{$property} = array();
		if (is_array($value)) {
			foreach ($value as $item) {
				$this->_addArrayPropertyItem($property, $item);
			}
		}
		else {
			$this->_addArrayPropertyItem($property, $value);
		}
	}
	protected function _addArrayPropertyItem($property, $value)
	{
		if (method_exists($this, '_verify'.$property.'Item')) {
			if (call_user_func(array($this, '_verify'.$property.'Item'), $value)) {
				$this->{$property}[] = $value;
			}
			else {
				throw new Exception('Unable to add '.$property.' item. Provided value is invalid.');
			}
		}
		else {
			$this->{$property}[] = $value;
		}
		return $this;
	}
	protected function _arrayPropertyItemForKey($property, $key)
	{
		if (isset($this->{$property}[$key]))
			return $this->{$property}[$key];
		return null;
	}
	protected function _setArrayPropertyItemForKey($property, $value, $key)
	{
		if (method_exists($this, '_verify'.$property.'Item')) {
			if (call_user_func(array($this, '_verify'.$property.'Item'), $value)) {
				$this->{$property}[$key] = $value;
			}
			else {
				throw new Exception('Unable to set '.$property.' for key “'.$key.'”. Provided value is invalid.');
			}
		}
		else {
			$this->{$property}[$key] = $value;
		}
		return $this;
	}
	protected function _removeArrayPropertyItemForKey($property, $key)
	{
		if (!empty($this->{$property}[$key]))
			array_splice($this->{$property}, $key, 1);
		return $this;
	}
	protected function _arrayPropertyKeys($property) {
		return array_keys($this->{$property});
	}
	protected function _arrayPropertyCount($property) {
		return count($this->{$property});
	}
	protected function _setPropertyAsInteger($property, $value)
	{
		$this->{$property} = intval($value);
		return $this;
	}
	protected function _setPropertyAsPositiveInteger($property, $value)
	{
		$value = intval($value);
		if ($value >= 0)
			$this->{$property} = $value;
		return $this;
	}
	protected function _setPropertyAsId($property, $value)
	{
		return $this->_setPropertyAsPositiveInteger($property, $value);
	}
	protected function _setPropertyAsBoolean($property, $value)
	{
		if ($value)
			$this->{$property} = true;
		else
			$this->{$property} = false;
		return $this;
	}
	protected function _setPropertyAsTimestamp($property, $value)
	{
		if (is_numeric($value))
			$this->{$property} = intval($value);
		else
			$this->{$property} = strtotime($value);
		return $this;
	}
	protected function _setPropertyAsFloat($property, $value)
	{
		$this->{$property} = floatval($value);
		return $this;
	}
	protected function _setPropertyAsEnumeratedValue($property, $value, $values)
	{
		if (in_array($value, $values))
			$this->{$property} = $value;
		else
			throw new Exception('Unable to set '.$property.'. Value does not match accepted values list.');
		return $this;
	}
	protected function _setPropertyAsObjectOfClass($property, $value, $class)
	{
		if ($value instanceof $class)
			$this->{$property} = $value;
		else
			throw new Exception('Unable to set '.$property.'. Value is not an instance of '.$class.'.');
		return $this;
	}
	protected function _getPropertyAsDate($property, $format = 'm/d/Y')
	{
		if (is_int($this->{$property}))
			return date($format, $this->{$property});
		return date($format, strtotime($this->{$property}));
	}
	protected function _getPropertyAsDatetime($property, $format = 'Y-m-d H:i:s')
	{
		return $this->_getPropertyAsDate($property, $format);
	}
}
class ELSWebAppKit_Settable_Model_Helper
{
	public static function methodsForClass($class)
	{
		return get_class_methods($class);
	}
	public static function methodExistsForClass($method, $class)
	{
		$method = strtolower($method);
		$methods = self::methodsForClass($class);
		foreach ($methods as $name)
			if (strtolower($name) == $method)
				return true;
		return false;
	}
}
