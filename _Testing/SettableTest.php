<?php
class ELSWAK_SettableTest
	extends PHPUnit\Framework\TestCase {
	
	public function testConstructor() {
		$var = new ELSWAK_Settable;
		$this->assertInstanceOf( 'ELSWAK_Settable', $var );
	}
}