<?php
/*
	ELSWAK Dictionary
	
	This class is essentially an enhanced associative array offering prebuilt behaviors for adding/setting/getting/removing values in a listing. Its primary purpose is to replace the many support methods within ELSWAK Settable that deal with such array properties.
*/

class ELSWAK_Dictionary_Exception extends ELSWAK_Exception {}
class ELSWAK_Dictionary_InvalidKey_Exception extends ELSWAK_Dictionary_Exception {}

class ELSWAK_Dictionary
	extends ELSWAK_Settable
	implements Iterator {
	
	/**
	 * Protected array used to store the dictionary contents.
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
	private $_uniqueKeyPrefix;
	/**
	 * Suffix used for creating unique keys
	 * @var string
	 */
	private $_uniqueKeySuffix;
	
	/**
	 * Construct a new dictionary with optional import values.
	 * @param array|object
	 */
	public function __construct($import = null, $prefix = 'Item-', $suffix = '') {
		$this->_position = 0;
		$this->setStore(array());
		$this->import($import);
		$this->_uniqueKeyPrefix = $prefix;
		$this->_uniqueKeySuffix = $suffix;
	}
	public function import($import) {
		// determine if the value is importable
		// import arrays and iterable objects
		if ( is_array($import) || (is_object($import) && $import instanceof Iterator ) ) {
			foreach ($import as $key => $value) {
				$this->set($key, $value);
			}
		}
		return $this;
	}
	
	protected function setStore(array $value) {
		// this method exists to protect the property from being set directly
		$this->store = $value;
		return $this;
	}
	
	
	
	/*
	 * Add the item to the collection, creating a key if none is provided or an item with that key exists.
	 * @return ELSWAK_Dictionary reference to this instance
	 */
	public function add($value, $key = null) {
		if ($key == null || $this->hasValueForKey($key)) {
			$key = $this->uniqueKeyForValue($value);
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
	 * @throws ELSWAK_Dictionary_InvalidKey_Exception
	 */
	public function valueForKeyWithException($key) {
		if ($this->hasValueForKey($key)) {
			return $this->store[$key];
		}
		throw new ELSWAK_Dictionary_InvalidKey_Exception('Unable to get value. Invalid key.');
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
	 * @throws ELSWAK_Dictionary_InvalidKey_Exception
	 */
	public function removeValueForKeyWithException($key) {
		if ($this->hasValueForKey($key)) {
			$value = $this->store[$key];
			unset($this->store[$key]);
			return $value;
		}
		throw new ELSWAK_Dictionary_InvalidKey_Exception('Unable to remove value. Invalid key.');
	}
	
	
		
	public function count() {
		return count($this->store);
	}
	public function hasItems() {
		return count($this->store) > 0;
	}
	
	
	
	/*
	 * Generate a unique key for the value.
	 *
	 * In this implementation it is superfluous to take the value as a parameter but subclasses may wish to overload this method with one which hashes or otherwise uses the value in key generation.
	 * @return string a unique key for the value
	 */
	public function uniqueKeyForValue($value = null) {
		$count = count($this->store) + 1;
		while (array_key_exists($this->_uniqueKeyPrefix.$count.$this->_uniqueKeySuffix, $this->store)) {
			++$count;
		}
		return $this->_uniqueKeyPrefix.$count.$this->_uniqueKeySuffix;
	}
	
	
	
	
//!Representation Methods
	public function __toString() {
		return json_encode($this->store, JSON_PRETTY_PRINT);
	}
	public function toJSON() {
		return json_encode($this->store, JSON_FORCE_OBJECT);
	}
	
	
	
//!Sorting Methods
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
	
	
	
//!Iterator Methods
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
}