<?php
class ELSWAK_Contact_CollectionTest
	extends PHPUnit\Framework\TestCase {

	public function testCollection() {
		$list = new ELSWAK_Contact_Collection;
		
		// setup something early to get coverage on the "other keys" section
		$this->assertNull($list->primary);
		$this->assertNull($list->home);

		// manually specify substitution priorities to ensure these remain consistent for tests
		ELSWAK_Contact_Collection::setSubstitutions(array(
			'primary' => array(
				'office',
				'work',
				'home',
			),
			'home' => array(
				'office',
				'work',
			),
			'work' => array(
				'office',
				'home',
			),
			'office' => array(
				'work',
				'home',
			),
		));

		$list->mobile = '662-555-0000';
		$this->assertEquals('662-555-0000', $list->primary);
		$this->assertEquals('662-555-0000', $list->bestForHome());
		$this->assertEquals('662-555-0000', $list->bestForWork());
		$this->assertEquals('662-555-0000', $list->bestForOffice());
		
		$list->home = '662-555-1111';
		$this->assertEquals('662-555-1111', $list->primary());
		$this->assertEquals('662-555-1111', $list->bestForOffice());
		
		$list->office = '662-555-2222';
		$this->assertEquals('662-555-2222', $list->primary());
		$this->assertEquals('662-555-2222', $list->bestForWork());
		
		$list->work = '662-555-3333';
		$this->assertEquals('662-555-2222', $list->primary());
		$this->assertEquals('662-555-3333', $list->bestForWork());
		
		$list->home = null;
		$this->assertEquals('662-555-2222', $list->bestForHome());
	}
}