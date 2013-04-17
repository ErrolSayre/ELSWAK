<?php
class ELSWAK_Value_SetTest
	extends PHPUnit_Framework_TestCase {

	public function testSet() {
		$var = new ELSWAK_Value_Set;
		$var->add('asdf');
		$this->assertEquals(1, $var->count());
		$var->add('asdf');
		$this->assertEquals(1, $var->count());
		$var->set('asdf', 'asdf');
		$this->assertEquals(1, $var->count());
		$var->add(false);
		$this->assertEquals(2, $var->count());
		$var->add('false');
		$this->assertEquals(3, $var->count());
		$var->add(null);
		$this->assertEquals(4, $var->count());
		$var->add(null);
		$this->assertEquals(4, $var->count());
	}
}