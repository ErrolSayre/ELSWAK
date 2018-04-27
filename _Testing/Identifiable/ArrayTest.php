<?php
class ELSWAK_Identifiable_ArrayTest
	extends PHPUnit\Framework\TestCase {

	public function testArray() {
		$var = new ELSWAK_Identifiable_Array;
		$var->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 25, 'name' => 'Twenty fifth')));
		$var->set('second', new ELSWAK_Identifiable_Array_Dummy(array('id' => 26, 'name' => 'Twenty sixth')));
		$var->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 27, 'name' => 'Twenty seventh')));

		$this->assertEquals(1, $var->positionForIdentifier(26));
		$this->assertEquals('second', $var->keyForIdentifier(26));
		$this->assertEquals(false, $var->positionForIdentifier(28));
	}
	public function testComparison() {
		$var1 = new ELSWAK_Identifiable_Array;
		$var1->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 25, 'name' => 'Twenty fifth')));
		$var1->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 26, 'name' => 'Twenty sixth')));
		$var1->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 27, 'name' => 'Twenty seventh')));
		$var2 = clone $var1;
		$diff = $var1->differences($var2);
		$this->assertFalse($diff->hasDifferences);
		
		$var2 = new ELSWAK_Identifiable_Array;
		$var2->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 26, 'name' => 'Twenty sixth')));
		$var2->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 25, 'name' => 'Twenty fifth')));
		$var2->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 27, 'name' => 'Twenty seventh')));
		$var2->set('fourth', new ELSWAK_Identifiable_Array_Dummy(array('id' => 28, 'name' => 'Twenty eighth')));
		
		// compare the two
		$diff = $var1->differences($var2);
		$this->assertTrue($diff->hasDifferences);
		$this->assertEquals(2, $diff->moved->count());
		$this->assertEquals(0, $diff->changed->count());
		$this->assertEquals(1, $diff->added->count());
		$this->assertEquals(0, $diff->removed->count());
		
		// make a change
		$var2->delete(0);
		$var2->item(0)->name = 'Twenty Fifth';
		$diff = $var1->differences($var2);
		$this->assertEquals(1, $diff->moved->count());
		$this->assertEquals(1, $diff->changed->count());
		$this->assertEquals(1, $diff->added->count());
		$this->assertEquals(1, $diff->removed->count());
		
		// make another change
		$var2->item(1)->name = 'Twenty SEVENTH';
		$diff = $var1->differences($var2);
		$this->assertEquals(1, $diff->moved->count());
		$this->assertEquals(1, $diff->moved[2]);
		$this->assertEquals(2, $diff->changed->count());
		$this->assertEquals(1, $diff->added->count());
		$this->assertEquals(28, $diff->added['fourth']->id);
		$this->assertEquals(1, $diff->removed->count());
	}
	/**
	 * Test the case where an identifiable array contains further
	 * identifiable arrays.
	 */
	public function testNestedComparison() {
		$var1 = new ELSWAK_Identifiable_Array;
		$var1->add(new ELSWAK_Identifiable_Array_DummyList);
		$var1->item(0)->setIdentifier('List 0');
		$var1->item(0)->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 25, 'name' => 'Twenty fifth')));

		$var1->add(new ELSWAK_Identifiable_Array_DummyList);
		$var1->item(1)->setIdentifier('List 1');
		$var1->item(1)->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 26, 'name' => 'Twenty sixth')));

		$var1->add(new ELSWAK_Identifiable_Array_DummyList);
		$var1->item(2)->setIdentifier('List 2');
		$var1->item(2)->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 27, 'name' => 'Twenty seventh')));

		$var2 = new ELSWAK_Identifiable_Array;
		$var2->add(new ELSWAK_Identifiable_Array_DummyList);
		$var2->item(0)->setIdentifier('List 0');
		$var2->item(0)->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 25, 'name' => 'Twenty fifth')));

		$var2->add(new ELSWAK_Identifiable_Array_DummyList);
		$var2->item(1)->setIdentifier('List 2');
		$var2->item(1)->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 27, 'name' => 'Twenty Seventh')));

		$var2->add(new ELSWAK_Identifiable_Array_DummyList);
		$var2->item(2)->setIdentifier('List 3');
		$var2->item(2)->add(new ELSWAK_Identifiable_Array_Dummy(array('id' => 28, 'name' => 'Twenty eighth')));
		
		$diff = $var1->differences($var2);
		$this->assertEquals(1, $diff->same->count());
		$this->assertEquals(1, $diff->moved->count());
		$this->assertEquals(1, $diff->changed->count());
		$this->assertEquals(1, $diff->added->count());
		$this->assertEquals(1, $diff->removed->count());
	}
	
	/**
	 * @expectedException ELSWAK_Array_InvalidItem_Exception
	 */
	public function testException() {
		// test for coverage
		$var = new ELSWAK_Identifiable_Array;
		$var->add(new stdClass);
	}
	/**
	 * @expectedException ELSWAK_Array_InvalidComparison_Exception
	 */
	public function testInvalidDifferences() {
		$var1 = new ELSWAK_Validated_Array;
		$var2 = new ELSWAK_Identifiable_Array;
		
		// var1 should be able to compare to var2 but not vice versa
		$this->assertInstanceOf('ELSWAK_Collection_Differences', $var1->differences($var2));
		
		$var2->differences($var1);
	}
}
class ELSWAK_Identifiable_Array_Dummy
	extends ELSWAK_Object
	implements ELSWAK_Identifiable {
	
	protected $id;
	protected $name;

	public function identifier() {
		return $this->id;
	}
}
class ELSWAK_Identifiable_Array_DummyList
	extends ELSWAK_Identifiable_Array
	implements ELSWAK_Identifiable {
	
	protected $identifier;

	public function setIdentifier($value) {
		$this->identifier = $value;
	}
	public function identifier() {
		return $this->identifier;
	}
}