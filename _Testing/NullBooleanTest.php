<?php
class ELSWAK_NullBooleanTest
	extends PHPUnit\Framework\TestCase {

	public function testValuesAndLabels() {
		$var = new ELSWAK_NullBoolean;
		$this->assertSame(null, $var->value);
		$this->assertEquals('n/a', $var->label);
		$this->assertEquals('maybe', $var->label('yes', 'no', 'maybe'));
		$var->value = 'n';
		$this->assertSame(false, $var->value);
		$this->assertEquals('no', "$var");
		$var->value = 'yes';
		$this->assertSame(true, $var->value);
		$this->assertEquals('yes', $var->label());
	}
}