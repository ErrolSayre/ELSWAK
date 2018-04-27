<?php
class ELSWAK_Object_ListTest
	extends PHPUnit\Framework\TestCase {

	/**
     * @expectedException ELSWAK_Array_InvalidItem_Exception
     */
	public function testArray() {
		$var = new ELSWAK_Object_List;
		
		// add an item
		$var->add( new ELSWAK_Object_ListTest_DummyClass );
		$this->assertEquals( 1, $var->length() );
		
		// try to add a non-object
		$var->add( 'ASDF' );
	}
}
class ELSWAK_Object_ListTest_DummyClass
	extends ELSWAK_Object {

	protected $name;

}