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
