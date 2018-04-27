<?php
class ELSWAK_Identifiable_SetTest
	extends PHPUnit\Framework\TestCase {

	public function testSet() {
		$set = new ELSWAK_Identifiable_Set;
		
		// add an item
		$item = new SetItem();
		$set->add($item);
		$this->assertEquals(1, $set->count());
		
		// try to add the same item again
		$set->set((string) $item, $item);
		$this->assertEquals(1, $set->count());
		
		// try to add a different instance with the same id
		$set->insert(new SetItem((string) $item));
		$this->assertEquals(1, $set->count());
		
		// add another item
		$set->add(new SetItem);
		$this->assertEquals(2, $set->count());
	}
}
class SetItem
	implements ELSWAK_Identifiable {

	protected $id;
	
	public function __construct($id = null) {
		if ($id == null) {
			$id = uniqid();
		}
		$this->id = $id;
	}
	public function identifier() {
		return $this->id;
	}
	public function __toString() {
		return $this->identifier();
	}
}