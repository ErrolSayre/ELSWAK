<?php
//!Related Exception Classes
/**
 * @package ELSWAK\Collections
 */
class ELSWAK_Array_Exception extends ELSWAK_Exception {}



/**
 * @package ELSWAK\Collections
 */
class ELSWAK_Array_InvalidKey_Exception extends ELSWAK_Array_Exception {
	public function __construct($message = null, $code = 0, Exception $previous = null) {
		if (!$message) {
			$message = 'Unable to get value. Invalid key provided.';
		}
		return parent::__construct($message, $code, $previous);
	}
}



/**
 * Exception for invalid item
 *
 * This exception is defiend here though it's not actually used by this
 * class. It is intended for use with the subclass ELSWAK Validated
 * Array.
 *
 * @package ELSWAK\Collections
 */
class ELSWAK_Array_InvalidItem_Exception extends ELSWAK_Array_Exception {}



/**
 * Exception for invalid comparison
 *
 * @package ELSWAK\Collections
 */
class ELSWAK_Array_InvalidComparison_Exception extends ELSWAK_Array_Exception {}



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
 * @package ELSWAK\Collections
 */
class ELSWAK_Array
	implements JsonSerializable, ArrayAccess, Iterator, ELSWAK_Differentiable, ELSWAK_Gettable {



//!Class memos
	/**
	 * Protected array used to store the contents.
	 * @type array
	 */
	protected $store;



//!Private Properties
	/**
	 * Position marker for use with iteration.
	 * @type int
	 */
	private $_position;
	/**
	 * Prefix used for creating unique keys
	 * @type string
	 */



//!Instance metadata
	/**
	 * Provide a collection of metadata
	 *
	 * This property provides a method for external entities to specify
	 * metadata for a collection such as marking the collection as complete
	 * or incomplete, or storing the timestamp it was populated.
	 *
	 * Currently the collection does no work to determine any of this
	 * metadata, however I may at a later date implement some automatic
	 * items. Despite this, it is trivial for a subclass to modify this to
	 * do so now. One possible item is a "clean/dirty" state, another would
	 * be a modification timestamp.
	 *
	 * @type array
	 */
	 protected $metadata = array();



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
	/**
	 * Give a copy of the store
	 *
	 * Since object properties are naturally copy on write, we don't need
	 * to copy the value. In reality this could be an alias of the store()
	 * method, but to facilitate subclasses altering the behavior of either
	 * method leave this duplication intact.
	 *
	 * @return array
	 */
	public function export() {
		$data = array();
		foreach ($this->store as $key => $value) {
			if ($value instanceof ELSWAK_Object) {
				$data[$key] = $value->_export();
			} elseif ($value instanceof ELSWAK_Array) {
				$data[$key] = $value->export();
			} else {
				$data[$key] = $value;
			}
		}
		return $data;
	}
	/**
	 * Empty the store
	 *
	 * Use this method to reset the contents of the array to empty.
	 *
	 * @return ELSWAK_Array self
	 */
	public function clear() {
		$this->store = array();
		return $this;
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

	/**
	 * Set a value
	 *
	 * @param mixed $value
	 * @param mixed $key
	 * @return ELSWAK_Array self
	 */
	public function setValueForKey($value, $key) {
		$this->store[$key] = $value;
		return $this;
	}



	/**
	 * @return mixed
	 */
	public function get($key = null) {
		return $this->valueForKey($key);
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
	/**
	 * Determine if a key is set
	 *
	 * This method mirrors (and utilizes) the array_key_exists function.
	 * Unfortunately it is not possible to abstract away the warnings from
	 * this method without significant investigation of types/values,
	 * accordingly values passed to this method won't be checked before
	 * passing along.
	 */
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



	/**
	 * Return the number of items in the array
	 *
	 * @return integer
	 */
	public function count() {
		return count($this->store);
	}
	/**
	 * Alias count
	 *
	 * @return integer
	 */
	public function length() {
		return $this->count();
	}
	public function hasItems() {
		return count($this->store) > 0;
	}
	public function isEmpty() {
		return count($this->store) == 0;
	}

	/**
	 * Determine if the array is associative
	 *
	 * This method lifts a creative solution from the internet. In a non-associative array, the keys
	 * (0, 1, 2, ... n) will themselves have keys of (0, 1, 2, ... n) whereas an associative array
	 * would not.
	 *
	 * @return boolean
	 */
	public function isAssociative() {
		
		// compare the keys of this array to their keys
		$keys = array_keys( $this->store );

		return array_keys( $keys ) !== $keys;
	}



	/**
	 * Return keys of the array
	 *
	 * @return array
	 */
	public function keys() {
		return array_keys($this->store);
	}

	/**
	 * Return key for a value
	 *
	 * The array_search call needs to be strict for arrays that contain
	 * mixed numeric and string values as otherwise searching for 0 will
	 * match the first string value.
	 *
	 * @param mixed $value
	 * @return integer|false index
	 */
	public function keyForValue($value) {
		return array_search($value, $this->store, true);
	}

	/**
	 * Return presence of value within array
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function hasValue($value) {
		if ( array_search( $value, $this->store ) !== false ) {
			return true;
		}
		return false;
	}

	/**
	 * Alias the hasValue method
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function contains($value) {
		return $this->hasValue($value);
	}

	/**
	 * Return position/item index for a value
	 *
	 * The array_search call needs to be strict for arrays that contain
	 * mixed numeric and string values as otherwise searching for 0 will
	 * match the first string value.
	 *
	 * @param mixed $value
	 * @return integer|false index
	 */
	public function positionForValue($value) {
		if (($key = array_search($value, $this->store, true)) !== false) {
			return $this->positionForKey($key);
		}
		return false;
	}

	/**
	 * Return position/item index for a key
	 *
	 * This method searches the array keys to determine its position which
	 * in turn corresponds to the "item index" within this collection.
	 *
	 * The array_search call needs to be strict for arrays that contain
	 * mixed integer and string keys as otherwise the string key value will
	 * map to 0 and match the first item.
	 *
	 * @param mixed $key
	 * @return integer|false index
	 */
	public function positionForKey($key) {
		return array_search($key, $this->keys(), true);
	}

	/**
	 * Return the key for the item at index
	 *
	 * @param $index
	 * @return mixed
	 */
	public function keyForItem($index) {
		$keys = $this->keys();
		if (array_key_exists($index, $keys)) {
			return $keys[$index];
		}
		return null;
	}

	/**
	 * Return the first key
	 *
	 * @return mixed
	 */
	public function firstKey() {
		$keys = $this->keys();
		return array_shift($keys);
	}

	/**
	 * Return the last key
	 *
	 * @return mixed
	 */
	public function lastKey() {
		$keys = $this->keys();
		return array_pop($keys);
	}



//!Item Accessors
	/**
	 * Mirror the DOM's item() method
	 *
	 * This method allows you to access non-numeric and mixed arrays in a
	 * numeric manner.
	 *
	 * @param integer $index
	 * @return mixed Item at $index
	 */
	public function item($index) {
		return $this->valueForKey($this->keyForItem($index));
	}



	/**
	 * Access the first item
	 *
	 * @return mixed|null
	 */
	public function first() {
		return $this->valueForKey($this->firstKey());
	}



	/**
	 * Access the last item
	 *
	 * @return mixed|null
	 */
	public function last() {
		return $this->valueForKey($this->lastKey());
	}



	/**
	 * Insert a value at index
	 *
	 * In order to most readily support this feature I'm using array_splice
	 * to do the heavy lifting. Unfortunately array_splice resets ALL
	 * numeric keys such that any skipped spaces are cleared. This isn't
	 * too important to me at this time but there may be a need for an
	 * array that can avoid this...
	 *
	 * In order to support a specific use-case I'm utilizing the special
	 * index of false to indicate an item should be inserted at the end.
	 *
	 * @param mixed $value
	 * @param integer|false $index
	 * @return ELSWAK_Array self
	 */
	public function insert($value, $index = false) {
		// if the index is false, simply append the value
		if ($index === false) {
			return $this->add($value);
		}

		// splice the value in at the array index
		// wrap the value in an array to ensure an array value remains that way
		array_splice($this->store, $index, 0, array($value));
		return $this;
	}



	/**
	 * Remove a value at index
	 *
	 * The insert and delete methods match add and remove with except that
	 * these methods deal with relative position within the array store
	 * rather than actual array keys.
	 *
	 * @param integer $index
	 * @return ELSWAK_Array self
	 */
	public function delete($index) {
		// remove the value in the array at the appropriate index
		array_splice($this->store, $index, 1);
		return $this;
	}



	/**
	 * Move a value from one position to another
	 *
	 * In order to support moving an item along with it's key, I'm
	 * utilizing an array replacement mechanism which preserves all
	 * non-integer keys. This means numeric string values such as ('2')
	 * will not be preserved but non-integer numeric string values such as
	 * '1.1' will be preserved. The integer determiniation is made by
	 * comparing typecast values of the same type —meaning I compare the
	 * string value of the key to the string cast integer value of the key.
	 *
	 * This is accomplished by creating a new array, pulling in all the
	 * keys and values up until the $to position, placing the value to be
	 * removed here, and then migrating values from the old store ignoring
	 * the value at $from.
	 *
	 * I could do this with array_splice, but rather than deal with the
	 * various splice points it's quicker and cleaner to me to do this
	 * iteratively.
	 *
	 * @param integer $from
	 * @param integer $to
	 * @return ELSWAK_Array self
	 */
	public function move($from, $to) {
		// only do this work if necessary
		$count = $this->count();
		if (
			$from != $to &&
			$from >= 0 &&
			$to   >= 0 &&
			$from <  $count &&
			$to   <  $count
		) {
			// create the new store
			$store = array();
			// iterate over the current array by key order
			$keys = $this->keys();
			$index = 0;
			// determine how to add the moved item based on its direction of travel
			$prepend = ($from > $to);
			while ($index < $count) {
				if ($index != $from) {
					if ($prepend && $index == $to) {
						// add the item to be moved, before the item currently at this position
						$key = $keys[$from];
						if ((string) ((int) $key) !== (string) $key) {
							// preserve this non-integer key
							$store[$key] = $this->store[$key];
						} else {
							// assign a new numeric key to this value
							$store[] = $this->store[$key];
						}
					}
					// copy the value at this position
					$key = $keys[$index];
					if ((string) ((int) $key) !== (string) $key) {
						// preserve this non-integer key
						$store[$key] = $this->store[$key];
					} else {
						// assign a new numeric key to this value
						$store[] = $this->store[$key];
					}

					// since the item is moving down the list, add it after the current item
					if (!$prepend && $index == $to) {
						$key = $keys[$from];
						if ((string) ((int) $key) !== (string) $key) {
							// preserve this non-integer key
							$store[$key] = $this->store[$key];
						} else {
							// assign a new numeric key to this value
							$store[] = $this->store[$key];
						}
					}
				}
				++$index;
			}
			// overwrite the store
			$this->store = $store;
		}
		return $this;
	}
	/**
	 * Move the item up in the list
	 *
	 * For the purposes of this class "up" is considered closer to the "top
	 * of the list" with the understanding that the list would naturally be
	 * presented in ascending order from top to bottom on a page.
	 *
	 * @param integer $index
	 * @return ELSWAK_Array self
	 */
	public function moveUp($index) {
		// only perform the move if reasonable to do so
		if ($index > 0 && $index < $this->count()) {
			$this->move($index, $index - 1);
		}
		return $this;
	}
	/**
	 * Move the item down in the list
	 *
	 * For the purposes of this class "down" is considered closer to the
	 * "bottom of the list" with the understanding that the list would
	 * naturally be presented in ascending order from top to bottom on a
	 * page.
	 *
	 * @param integer $index
	 * @return ELSWAK_Array self
	 */
	public function moveDown($index) {
		// only perform the move if reasonable to do so
		if ($index >= 0 && $index < $this->count() - 1) {
			$this->move($index, $index + 1);
		}
		return $this;
	}



//!Queue & Stack methods
	/**
	 * Mirror PHP's array_shift
	 *
	 * @return mixed|null
	 */
	public function shift() {
		return $this->removeValueForKey($this->firstKey());
	}



	/**
	 * Mirror PHP's array_unshift
	 *
	 * This is a very special case, as adding items to the front of the
	 * array needs to be done in a manner that is compatible with the
	 * various subclasses. Subclasses will need to provide an override to
	 * this method to ensure things such as validation and auto-key-
	 * generation are done properly.
	 *
	 * At this point it looks like array_splice is the best option.
	 *
	 * @return mixed|null
	 */
	public function unshift($value) {
		return $this->insert($value, 0);
	}



	/**
	 * Mirror PHP's array_pop
	 *
	 * @return mixed|null
	 */
	public function pop() {
		return $this->removeValueForKey($this->lastKey());
	}



	/**
	 * Mirror PHP's array_push
	 *
	 * The add method already has this behavior.
	 *
	 * @return ELSWAK_Array self
	 */
	public function push($value) {
		return $this->add($value);
	}




//!Representation
	public function formattedList($delimiter = ', ', $prefix = '', $suffix = '') {
		if ($this->hasItems()) {
			return $prefix.implode($delimiter, $this->store).$suffix;
		}
		return '';
	}
	public function join($conjunction = false, $useOxfordComma = true, $separator = ',') {
		return ELSWAK_Array_Utilities::joinWithOptions($this->store, $conjunction, $useOxfordComma, $separator);
	}
	public function joinToEnglishListing($conjunction = 'and', $useOxfordComma = true, $separator = ',') {
		return ELSWAK_Array_Utilities::joinWithOptions($this->store, $conjunction, $useOxfordComma, $separator);
	}
	public function httpQueryString($prefix = '', $separator = '&') {
		return http_build_query($this->store, $prefix, $separator);
	}
	public function __toString() {
		return json_encode($this->store, JSON_PRETTY_PRINT);
	}
	public function toJSON( $pretty = false ) {
		
		$options = null;
		if ( $pretty ) {
			$options = JSON_PRETTY_PRINT;
		}
		
		return json_encode( $this->store, $options );
	}



//!Sorting
	public function sort( $reverse = false ) {
		return $this->sortByValue( $reverse);
	}
	
	/**
	 * Sort the array by its contents.
	 *
	 * Please note that previously this method always used the asort and arsort methods, however this
	 * has proven dubious for non-associative arrays. PHP doesn't directly indicate that an array is
	 * or isn't associative so for a long time this class has assumed associative and therefore
	 * incorrectly handled non-associative array indexes.
	 *
	 * To address this issue, we not utilize a quick check to determine if the array is associative
	 * by examinging the makeup of the keys.
	 */
	public function sortByValue( $reverse = false, $flags = null ) {
		if ( $flags === null ) {
			$flags = SORT_NATURAL | SORT_FLAG_CASE;
		}
		
		// determine if this array is associative or not
		if ( $this->isAssociative() ) {
			if ( $reverse ) {
				arsort( $this->store, $flags );
			}
			else {
				asort( $this->store, $flags );
			}
		}
		else {
			if ( $reverse ) {
				rsort( $this->store, $flags );
			}
			else {
				sort( $this->store, $flags );
			}
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



//!Magic Accessors
	public function __get($key) {
		return $this->valueForKey($key);
	}
	public function __set($key, $value) {
		return $this->setValueForKey($value, $key);
	}



//!Iterator methods
	public function rewind() {
		$this->_position = 0;
		return $this;
	}
	/**
	 * Skip to the last item
	 *
	 * This isn't an iterator method but it fits here.
	 *
	 * @return self
	 */
	public function fastForward() {
		$this->_position = count( $this->store ) - 1;
		return $this;
	}
	public function current() {
		if ( ( $key = $this->key() ) !== null ) {
			return $this->store[ $key ];
		}
		return false;
	}
	public function key() {
		return $this->keyForItem( $this->_position );
	}



	/**
	 * Move the internal pointer to the next item
	 *
	 * The Iterator documentation specifies that any return value is
	 * ignored. Accordingly, we'll return $this in order to allow chaining
	 * e.g.
	 *     $var->next()->next();
	 *
	 * There are also situations where one would want to get the next item
	 * so this class provides several variations of these methods.
	 *
	 * @return self
	 */
	public function next() {
		++$this->_position;
		return $this;
	}
	public function valid() {
		$keys = array_keys($this->store);
		return array_key_exists($this->_position, $keys);
	}



//!Iterator-like methods
	/**
	 * Move to and get next item
	 *
	 * Extending the Iterator interface, this shortcut moves to the next
	 * item and returns it directly.
	 *
	 * @return mixed
	 */
	public function nextItem() {
		return $this->next()->current();
	}
	/**
	 * Move backward
	 *
	 * This isn't an iterator method, but it fits here nicely
	 *
	 * @return mixed
	 */
	public function previous() {
		--$this->_position;
		return $this;
	}
	/**
	 * Move to and get previous item
	 *
	 * @return mixed
	 */
	public function previousItem() {
		return $this->previous()->current();
	}
	/**
	 * Move to the end and get the last item
	 *
	 * @return mixed
	 */
	public function lastItem() {
		return $this->fastForward()->current();
	}
	/**
	 * Skip to a position
	 *
	 * Search the array for the key and set the internal position
	 * there.
	 *
	 * When a value is not found, the position set out of bounds such that
	 * current() will return false and next() will return item 0 if set.
	 *
	 * @param mixed $search
	 * @return ELSWAK_Array self
	 */
	public function skipToKey($search) {
		$position = $this->positionForKey($search);
		if ($position !== false) {
			$this->_position = $position;
		} else {
			$this->_position = -1;
		}
		return $this;
	}
	/**
	 * Skip to a position by value
	 *
	 * Search the array for the value and set the internal position
	 * there.
	 *
	 * Don't assume, eveng in a purely numeric array, that the value's key
	 * can be trusted to line up with it's true position in the store.
	 *
	 * When a value is not found, the position set out of bounds such that
	 * current() will return false and next() will return item 0 if set.
	 *
	 * @param mixed $search
	 * @return ELSWAK_Array self
	 */
	public function skipToValue($search) {
		$position = $this->positionForValue($search);
		if ($position !== false) {
			$this->_position = $position;
		} else {
			$this->_position = -1;
		}
		return $this;
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
	 * This will only work if the property is able to be set to null, which
	 * may or may not be allowed based upon the extending class definition.
	 *
	 * @return mixed|ELSWAK_Object self
	 */
	public function offsetUnset($offset) {
		return $this->removeValueForKey($offset);
	}

	/**
	 * Get a property via array notation.
	 * @return mixed|null
	 */
	public function offsetGet($offset) {
		return $this->valueForKey($offset);
	}



//!JSONSerializable methods
	/**
	 * Provide the JSON encoder with an easy to handle array.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->store;
	}



//!Item Parsing
	/**
	 * Validate an item within the store.
	 * @param mixed $item
	 * @param boolean $returnValue Should the item value be returned instead of the key?
	 * @return mixed Key or value of the item
	 */
	public function parseItemFromKeysAndValues($item, $returnValue = false, $allowSubstrings = true) {
		return $this->parseItemFromArrayKeysAndValues($item, $this->store, $returnValue, $allowSubstrings);
	}

	/**
	 * Alias the validation method to be shorter
	 * @see parseItemFromKeysAndValues
	 */
	public function parseItem($item, $returnValue = false, $allowSubstrings = true) {
		return $this->parseItemFromKeysAndValues($item, $returnValue, $allowSubstrings);
	}



//!Array Comparison
	/**
	 * Compare this array to another
	 *
	 * Collect differences in a collection differences object.
	 * @see ELSWAK_Collection_Differences
	 *
	 * Since this method is expected to be subclassed, the comparison
	 * object must be validated within the method.
	 *
	 * @param mixed $compare
	 * @return ELSWAK_Collection_Differences
	 */
	public function differences($compare) {
		// validate the comparison object is of the same type as this variable
		if ($compare instanceof $this) {
			$diff = new ELSWAK_Collection_Differences;

			// look through the comparison object and the local store for matches
			foreach ($this->store as $key => $item) {
				// look for a match in the comparison object
				if ($compare->valueForKey($key) == $item) {
					$diff->same->setValueForKey($item, $key);
				} else {
					// look for the value as it may have moved
					$cKey = $compare->keyForValue($item);
					if ($cKey === false) {
						// the value has been removed
						$diff->removed->setValueForKey($item, $key);
					} else {
						// the value has been moved
						$diff->moved->setValueForKey($cKey, $key);
					}
				}
			}
			// now look for items in the comparison that don't exist locally
			$diff->added->setStore(array_diff($compare->store, $this->store));

			// return the differences
			return $diff;
		}
		throw new ELSWAK_Array_InvalidComparison_Exception('Unable to compare objects. Comparison must be made against like types.');
	}



//!Metadata
	public function metadata() {
		return $this->metadata;
	}
	/**
	 * Provide a combined shortcut accessor
	 *
	 * This method can both get and set values depending upon the supplied
	 * parameters. Since a non-existent key will return a null value, it is
	 * relatively safe to utilize this as the flag to indicate behaving as
	 * a getter. This does has the side-effect that if someone wanted the
	 * null value to actually be set within the internal storage, they
	 * would need to utilize the setMetadataForKey method.
	 */
	public function md($key, $value = null) {
		if ($value !== null) {
			return $this->setMetadataForKey($value, $key);
		}
		return $this->metadataForKey($key);
	}
	public function metadataForKey($key) {
		if (array_key_exists($key, $this->metadata())) {
			return $this->metadata[$key];
		}
		return null;
	}
	public function setMetadataForKey($value, $key) {
		$this->metadata[$key] = $value;
		return $this;
	}



//!Static methods
	public static function parseItemFromArrayKeysAndValues($item, array $items, $returnValue = false, $allowSubstrings = true) {
		// first look for the value as a key
		if (array_key_exists($item, $items)) {
			return $returnValue? $items[$item]: $item;
		}

		// look for the value in the labels
		if ($item) {
			$item = strtolower($item);
			foreach ($items as $key => $value) {
				// compare the item as a substring to the value
				$compare = strtolower($value);
				if (
					(
						$allowSubstrings &&
						strpos($compare, $item) !== false
					) ||
					$compare == $item
				) {
					return $returnValue? $value: $key;
				}
			}
		}
		return null;
	}
}