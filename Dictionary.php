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
	 * Private position variable for use with iteration.
	 * @var int
	 */
	private $position;
	/**
	 * Protected array used to store the dictionary contents.
	 * @var array
	 */
	protected $store;
	
	/**
	 * Construct a new dictionary with optional import values.
	 * @param array|object
	 */
	public function __construct($import = null) {
		$this->position = 0;
		$this->setStore(array());
		$this->import($import);
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
	 * Add the item to the collection if there is not already an item with that value or there is a way to auto-generate a key.
	 * @return ELSWAK_Dictionary reference to this instance
	 */
	public function add($value, $key = null) {
		if ($key == null) {
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
		throw new ELSWAK_Dictionary_InvalidKey_Exception('Unable to get value. Invalid key.');
	}
	public function has($key) {
		return $this->hasValueForKey($key);
	}
	public function hasValueForKey($key) {
		return array_key_exists($key, $this->store);
	}
	public function remove($key) {
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
	/*
	 * Generate a unique key for the value.
	 *
	 * In this implementation it is superfluous to take the value as a parameter but subclasses may wish to overload this method with one which hashes or otherwise uses the value in key generation.
	 * @return string a unique key for the value
	 */
	public function uniqueKeyForValue($value = null) {
		$count = count($this->store);
		$prefix = $this->generatedKeyPrefix();
		$suffix = $this->generatedKeySuffix();
		while (array_key_exists($prefix.$count.$suffix, $this->store)) {
			++$count;
		}
		return $prefix.$count.$suffix;
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
		$this->position = 0;
	}
	public function current() {
		return $this->store[$this->key()];
	}
	public function key() {
		$keys = array_keys($this->store);
		if (array_key_exists($this->position, $keys)) {
			return $keys[$this->position];
		}
		return null;
	}
	public function next() {
		++$this->position;
	}
	public function valid() {
		$keys = array_keys($this->store);
		return array_key_exists($this->position, $keys);
	}
	
	
	
//!Class Methods
	public static function generatedKeyPrefix() {
		return 'Item-';
	}
	public static function generatedKeySuffix() {
		return '';
	}
}