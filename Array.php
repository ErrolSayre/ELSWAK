<?php
//!Related Exception Classes
class ELSWAK_Array_Exception extends ELSWAK_Exception {}
class ELSWAK_Array_InvalidKey_Exception extends ELSWAK_Array_Exception {
	public function __construct($message = null, $code = 0, Exception $previous = null) {
		if (!$message) {
			$message = 'Unable to get value. Invalid key provided.';
		}
		return parent::__construct($message, $code, $previous);
	}
}

//!Stub Constants
if (!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 0);
}

// Place a stub for backward compatibility with PHP < 5.4.0
if (!interface_exists('JsonSerializable')) {
	require_once 'JsonSerializeableInterface.php';
}

/**
 * Wrap a normal array in a class that makes its values accessible like object properties.
 *
 * This class is essentially an enhanced associative array offering
 * prebuilt behaviors for adding/setting/getting/removing values in a
 * listing.
 *
 * Its primary purpose is to replace the many support methods within
 * ELSWAK Settable that deal with such array properties.
 *
 * @package ELSWAK
 */
class ELSWAK_Array
	implements JsonSerializable, ArrayAccess, Iterator {

	/**
	 * Protected array used to store the contents.
	 * @var array
	 */
	protected $store;

//!Private Properties
	/**
	 * Position marker for use with iteration.
	 * @var int
	 */
	private $_position;
	/**
	 * Prefix used for creating unique keys
	 * @var string
	 */

	/**
	 * Construct a new array object
	 * @param array $data the array to wrap
	 */
	public function __construct($import = null) {
		$this->_position = 0;
		$this->setStore($import);
	}
	public function import($import) {
		// determine if the value is importable
		// import arrays and iterable objects
		if (is_array($import) || $import instanceof Traversable) {
			foreach ($import as $key => $value) {
				$this->set($key, $value);
			}
		}
		return $this;
	}
	public function export() {
		return $this->store;
	}



	/**
	 * Set the store
	 * @param mixed $data the array to wrap or object to import
	 * @return ELSWAK_Array self
	 */
	public function setStore($data = null) {
		if (is_array($data)) {
			$this->store = $data;
		} else {
			$this->store = array();
			return $this->import($data);
		}
		return $this;
	}

	/**
	 * Get the store
	 * @return array the wrapped array
	 */
	public function store() {
		return $this->store;
	}



	/*
	 * Add the item to the collection, appending if no key is provided or an item with that key exists.
	 * @return ELSWAK_Array reference to this instance
	 */
	public function add($value, $key = null) {
		if ($key == null || $this->hasValueForKey($key)) {
			$this->store[] = $value;
			return $this;
		}
		return $this->setValueForKey($value, $key);
	}
	public function set($key, $value) {
		return $this->setValueForKey($value, $key);
	}
	public function setValueForKey($value, $key) {
		$this->store[$key] = $value;
		return $this;
	}



	/*
	 * Allow the caller to get a single item, or the entire collection with the same method honoring the behavior for invalid keys.
	 * @return mixed individual value or entire collection
	 */
	public function get($key = null) {
		if ($key != null) {
			return $this->valueForKey($key);
		}
		return $this->store;
	}
	public function valueForKey($key) {
		if ($this->hasValueForKey($key)) {
			return $this->store[$key];
		}
		return null;
	}
	/*
	 * Behave like valueForKey but throw an exception on an invalid key.
	 * @return mixed
	 * @throws ELSWAK_Array_InvalidKey_Exception
	 */
	public function valueForKeyWithException($key) {
		if ($this->hasValueForKey($key)) {
			return $this->store[$key];
		}
		throw new ELSWAK_Array_InvalidKey_Exception;
	}
	public function has($key) {
		return $this->hasValueForKey($key);
	}
	public function hasValueForKey($key) {
		return array_key_exists($key, $this->store);
	}
	public function remove($key) {
		return $this->removeValueForKey($key);
	}
	public function removeValueForKey($key) {
		if ($this->hasValueForKey($key)) {
			$value = $this->store[$key];
			unset($this->store[$key]);
			return $value;
		}
		return null;
	}
	/*
	 * Behave like removeValueForKey but throw an exception on an invalid key.
	 * @return mixed
	 * @throws ELSWAK_Array_InvalidKey_Exception
	 */
	public function removeValueForKeyWithException($key) {
		if ($this->hasValueForKey($key)) {
			$value = $this->store[$key];
			unset($this->store[$key]);
			return $value;
		}
		throw new ELSWAK_Array_InvalidKey_Exception('Unable to remove value. Invalid key provided.');
	}



	public function count() {
		return count($this->store);
	}
	public function hasItems() {
		return count($this->store) > 0;
	}

	public function keys() {
		return array_keys($this->store);
	}




//!Representation methods
	public function __toString() {
		return json_encode($this->store, JSON_PRETTY_PRINT);
	}
	public function toJSON() {
		return json_encode($this->store, JSON_FORCE_OBJECT);
	}



//!Sorting methods
	public function sort($reverse = false) {
		return $this->sortByValue($reverse);
	}
	public function sortByValue($reverse = false, $flags = null) {
		if ($flags === null) {
			$flags = SORT_NATURAL | SORT_FLAG_CASE;
		}
		if ($reverse) {
			arsort($this->store, $flags);
		} else {
			asort($this->store, $flags);
		}
		return $this;
	}
	public function sortByKey($reverse = false, $flags = null) {
		if ($flags === null) {
			$flags = SORT_NATURAL | SORT_FLAG_CASE;
		}
		if ($reverse) {
			krsort($this->store, $flags);
		} else {
			ksort($this->store, $flags);
		}
		return $this;
	}



//!Magic Accessor methods
	public function __get($key) {
		return $this->valueForKey($key);
	}
	public function __set($key, $value) {
		return $this->setValueForKey($value, $key);
	}



//!Iterator methods
	public function rewind() {
		$this->_position = 0;
	}
	public function current() {
		return $this->store[$this->key()];
	}
	public function key() {
		$keys = array_keys($this->store);
		if (array_key_exists($this->_position, $keys)) {
			return $keys[$this->_position];
		}
		return null;
	}
	public function next() {
		++$this->_position;
	}
	public function valid() {
		$keys = array_keys($this->store);
		return array_key_exists($this->_position, $keys);
	}



//!ArrayAccess methods
	/**
	 * Set a property via array notation.
	 * @return mixed|ELSWAK_Object self
	 */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			return $this->add($value);
		}
		return $this->setValueForKey($value, $offset);
	}

	/**
	 * Determine if the offset is "gettable" as a real or virtual property.
	 * @return boolean
	 */
	public function offsetExists($offset) {
		return $this->hasValueForKey($offset);
	}

	/**
	 * Restore the property to a null state.
	 *
	 * This will only work if the property is able to be set to null, which may or may not be allowed based upon the extending class definition.
	 * @return mixed|ELSWAK_Object self
	 */
	public function offsetUnset($offset) {
		return $this->removeValueForKey($offset);
	}

	/**
	 * Get a property via array notation.
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this->valueForKey($offset);
	}



//!JsonSerializable methods
	/**
	 * Provide the JSON encoder with an easy to handle array.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->store;
	}
}