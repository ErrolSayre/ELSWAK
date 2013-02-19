<?php
class ELSWAK_Validated_ArrayTest
	extends PHPUnit_Framework_TestCase {

	public function testArray() {
		$var = new ELSWAK_Validated_Array;
		
		// add an item
		$var->add('Zero');
		$this->assertEquals('Zero', $var->get(0));
		
		// add an item at position "1"
		$var->add('One', '1');
		$this->assertTrue($var->hasValueForKey(1));
		
		// add an item at position "Three"
		$var->add('Three', 'Three');
		
		// add a new item without a position
		$var->add('Two');
		// PHP behavior (as of PHP 5.4.9 and earlier) is to set this value at index 2, ensure this class does the same
		$this->assertTrue($var->hasValueForKey(2));
		$this->assertEquals('Two', $var[2]);
		
		// ensure that the setter doesn't also append
		$var[2] = 'TWO';
		$this->assertEquals('TWO', $var[2]);
	}
	
	public function testValidation() {
		$var = new CrazyDummyArray;
		
		// try to add a non CDA value
		$var->add('Yer mum');
		$this->assertInstanceOf('CrazyDummyArray', $var[0]);
	}
}
class CrazyDummyArray
	extends ELSWAK_Validated_Array {


	/**
	 * Ensure this array can only contain other CrazyDummyArrays
	 */
	public function validateOrTransformItemForInclusion($item) {
		if (!($item instanceof CrazyDummyArray)) {
			return new CrazyDummyArray($item);
		}
		return $item;
	}
}