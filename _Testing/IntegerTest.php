<?php
class ELSWAK_IntegerTest
	extends PHPUnit_Framework_TestCase {

	public function testInteger() {
		$int = new ELSWAK_Integer('3013593.2304');
		$this->assertSame(3013593, $int->value);
		$this->assertSame(0, $int->positiveIntegerForValue(-3952104.3914));
		$this->assertSame(3952104, $int->positiveIntegerForValue('3952104.3914qwefasd'));
	}
}