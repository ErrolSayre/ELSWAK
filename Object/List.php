<?php
/**
 * @author Errol Sayre
 */
/**
 * List of objects
 *
 * This class provides a base for "list of objects" collections. It simplifies the process over
 * ELSWAK Validated Array by only requiring subclasses to override the requiredClass method.
 *
 * Please note that there is not requirement that the required class be a descendent of
 * ELSWAK_Object.
 */
class ELSWAK_Object_List
	extends ELSWAK_Validated_Array {



//!Validated Array — Instance methods
	/**
	 * Override parent method to ensure type
	 *
	 * @param mixed $item
	 * @return object An object of the required class
	 * @throws ELSWAK_Array_InvalidItem_Exception
	 */
	public function validateOrTransformItemForInclusion( $item ) {
		// for some reason PHP won't accept a method call after instanceof
		$class = $this->requiredClass();
		if ( $item instanceof $class ) {
			return $item;
		}
		// the item is not of the acceptable class
		throw new ELSWAK_Array_InvalidItem_Exception( 'Unable to add item to list: item must be an instance of ' . $class . '.' );
	}



//!Static methods
	public static function requiredClass() {
		return 'ELSWAK_Object';
	}
}