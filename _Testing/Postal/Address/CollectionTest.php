<?php
class ELSWAK_Postal_Address_CollectionTest
	extends PHPUnit_Framework_TestCase {

	public function testCollection() {
		$list = new ELSWAK_Postal_Address_Collection;
		$list->work = '217 Powers Hall'.LF
			.'University, MS 38677';
		$this->assertInstanceOf('ELSWAK_Postal_Address', $list->primary);
		$list->work = '';
	}
}