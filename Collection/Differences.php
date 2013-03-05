<?php
//!Related exceptions
/**
 * @package ELSWAK\Exceptions
 */
class ELSWAK_Collection_Differences_Exception
	extends ELSWAK_Exception {}

/**
 * @package ELSWAK\Exceptions
 */
class ELSWAK_Collection_Differences_InvalidProperty_Exception
	extends ELSWAK_Collection_Differences_Exception {}



/**
 * ELSWAK Collection Differences
 *
 * Collection differences are defined as items moved, changed, added, or
 * removed relative to the current ($this) collection. Items can be
 * moved to a different key, their properties modified, present in the
 * compared collection but not the current (added), or vice versa
 * (removed).
 *
 * To state it another way, the comparison assumes that the object being
 * compared to this object is a later version so additions/deletions are
 * relative to that perspective.
 *
 * Please note scalar values and even collections which don't have
 * means to specifically identify items aren't considered changed but
 * either:
 * - same: value/item is at same key in both collections
 * - moved: same value/item found at different keys in each collection
 * - added: value/item not found in target object
 * - removed: value/item not found in compared object
 *
 * This is a byproduct of the lack of mechanism to make deeper
 * comparisons. For sub-class collections which know their items match a
 * certain profile, they can provide such properties to the comparison
 * algorithm by implementing the ELSWAK Collection ItemIdentifier
 * interface.
 *
 * @package ELSWAK\Collections
 */
class ELSWAK_Collection_Differences {



	/**
	 * Items with the same key with no changed properties.
	 *
	 * @type ELSWAK_Array
	 */
	protected $same;

	/**
	 * Items with different keys but otherwise identical.
	 *
	 * This listing is intended to be a collection of keys from the
	 * comparison collection indexed by the key from the target collection.
	 *
	 * In other words "item at [key] moved to key [value]".
	 *
	 * @type ELSWAK_Array
	 */
	protected $moved;

	/**
	 * Items in the same position but have changed properties.
	 *
	 * This listing is intended to collect the items from the comparison
	 * object. I.e. changes that should be applied to the target object to
	 * bring it into alignment.
	 *
	 * Please note that the keys of this array will be the corresponding
	 * key from the item in the comparison collection.
	 *
	 * @type ELSWAK_Array
	 */
	protected $changed;

	/**
	 * Items that are not in the target collection but are in the compared.
	 *
	 * Please note that the keys of this array will be the corresponding
	 * key from the item in the comparison collection.
	 *
	 * @type ELSWAK_Array
	 */
	protected $added;

	/**
	 * Items that are in the target collection but not in the compared.
	 *
	 * Keys and values in this list come from the target collection.
	 *
	 * @type ELSWAK_Array
	 */
	protected $removed;



	public function same() {
		if (!$this->same instanceof ELSWAK_Array) {
			$this->same = new ELSWAK_Array;
		}
		return $this->same;
	}



	public function moved() {
		if (!$this->moved instanceof ELSWAK_Array) {
			$this->moved = new ELSWAK_Array;
		}
		return $this->moved;
	}



	public function changed() {
		if (!$this->changed instanceof ELSWAK_Array) {
			$this->changed = new ELSWAK_Array;
		}
		return $this->changed;
	}



	public function added() {
		if (!$this->added instanceof ELSWAK_Array) {
			$this->added = new ELSWAK_Array;
		}
		return $this->added;
	}



	public function removed() {
		if (!$this->removed instanceof ELSWAK_Array) {
			$this->removed = new ELSWAK_Array;
		}
		return $this->removed;
	}



	public function hasDifferences() {
		// check each of the difference properties to see if any have values
		foreach (self::differenceProperties() as $property) {
			if ($this->{$property} instanceof ELSWAK_Array) {
				if ($this->{$property}->hasItems()) {
					return true;
				}
			}
		}
		return false;
	}



	/**
	 * Allow properties to be gettable
	 *
	 * @param string $property
	 * @return ELSWAK_Array
	 */
	public function __get($property) {
		if (method_exists($this, $property)) {
			return $this->{$property}();
		}
		throw new ELSWAK_Collection_Differences_InvalidProperty_Exception('Unable to get property "'.$property.'". Property is not defined within the class "'.get_class($this).'".');
	}



	protected static function differenceProperties() {
		return array(
			'moved',
			'changed',
			'added',
			'removed',
		);
	}
}