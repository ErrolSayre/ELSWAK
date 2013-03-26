<?php
/**
 * ELSWAK Identifiable Array
 *
 * Array of identifiable objects. Extend the validated array to ensure
 * each item is implements ELSWAK Identifiable.
 *
 * The primary purpose of this collection is to provide a way to compare
 * items in two collections that have an identifier. This allows
 * checking for changed values as well as positional changes of complex
 * types which would otherwise manifest as an unknown pair of removal
 * and addition.
 *
 * @package ELSWAK\Collections
 */
class ELSWAK_Identifiable_Array
	extends ELSWAK_Validated_Array {



	/**
	 * Maintain an index of identifiers
	 *
	 * This will aid in returning a particular item by its identifier.
	 *
	 * @type array
	 */
	protected $identifierIndex = array();



	/**
	 * Override the parent method
	 *
	 * Ensure that items added are verified to be identifiable.
	 *
	 * Please note that sub-classes of this array type will need to call
	 * this parent method on items they intend to verify also.
	 *
	 * @param mixed $item
	 * @return mixed $item
	 */
	public function validateOrTransformItemForInclusion($item) {
		if (!($item instanceof ELSWAK_Identifiable)) {
			throw new ELSWAK_Array_InvalidItem_Exception('Unable to add item. Item must be identifiable.');
		}
		return $item;
	}



	/**
	 * Locate an item by its identifier
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function itemForIdentifier($value) {
		if (array_key_exists($value, $this->identifierIndex)) {
			$key = $this->identifierIndex[$value];
			if ($this->hasValueForKey($key)) {
				$item = $this->valueForKey($key);
				if ($item->identifier() == $value) {
					return $item;
				}
			}
			// remove the false index
			unset($this->identifierIndex[$value]);
		}
		
		// since the value isn't known, search for it
		foreach ($this->store as $key => $item) {
			// since we're looking at this record, make sure it's index is up to date
			$this->identifierIndex[$item->identifier()] = $key;
			if ($item->identifier() == $value) {
				return $item;
			}
		}
		return false;
	}
	public function keyForIdentifier($value) {
		// check (and prime) the index
		if (($item = $this->itemForIdentifier($value)) !== false) {
			return $this->identifierIndex[$value];
		}
		return false;
	}
	/**
	 * Locate an item's index by its identifier
	 *
	 * @param mixed $value
	 * @return integer
	 */
	public function positionForIdentifier($value) {
		return $this->positionForKey($this->keyForIdentifier($value));
	}



	/**
	 * Compare two lists
	 *
	 * Override the parent method to collect differences in a collection
	 * differences object.
	 *
	 * Since this is a collection of identifiable items, utilize the
	 * identifier to determine if an item is the same from one list to
	 * another (allowing indication of change).
	 *
	 * Since this method is expected to be subclassed, the comparison
	 * object must be validated within the method.
	 *
	 * @param mixed $compare
	 * @return ELSWAK_Collection_Differences
	 */
	public function differences($compare) {
		// validate the comparison object is of the same type as this variable
		if ($this !== $compare && $compare instanceof $this) {
			$diff = new ELSWAK_Collection_Differences;
			
			// look through the the comparison object and the local store for matches
			$seen = array();
			foreach ($this->store as $key => $item) {
				// mark this identifier as seen
				$seen[$item->identifier()] = true;
				
				// look for a matching identifier in the other listing
				if (($compareItem = $compare->itemForIdentifier($item->identifier())) !== false) {
					// the item was found, determine the key
					$compareKey = $compare->keyForIdentifier($item->identifier());
					
					// determine if the position has changed
					if ($key != $compareKey) {
						$diff->moved[$key] = $compareKey;
					}
					
					// determine if the value has changed
					if ($item instanceof ELSWAK_Differentiable && $compareItem instanceof ELSWAK_Differentiable) {
						// since the objects implement the ELSWAK_Differentiable interface, we can utilize the diff method
						$itemDiff = $item->differences($compareItem);
						if ($diff->hasDifferences) {
							// keep the changed copy for this diff
							$diff->changed[$compareKey] = $compareItem;
						}
					// utilize the PHP built-in comparison (which looks at every property)
					} elseif ($item != $compareItem) {
						$diff->changed[$compareKey] = $compareItem;
					}
				} else {
					// the item does not exist in the comparison object
					$diff->removed[$key] = $item;
				}
			}
			
			// now look for added items
			foreach ($compare->store as $key => $item) {
				if (!array_key_exists($item->identifier(), $seen)) {
					$diff->added[$key] = $item;
				}
			}
			
			return $diff;
		}
		throw new ELSWAK_Array_InvalidComparison_Exception('Unable to compare objects. Comparison must be made against different objects of like types.');
	}
}