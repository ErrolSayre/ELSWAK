<?php
class ELSWAK_BooleanTest
	extends PHPUnit_Framework_TestCase {
	
	public function testValues() {
		$var = new ELSWAK_BooleanTest_Dummy;
		$this->assertTrue($var->active);
		$var->active = 'no';
		$this->assertFalse($var->active);
		$var->setActive('x');
		$this->assertTrue($var->active);
		$var->setActive('marked');
		$this->assertFalse($var->active);
		$var->setActive('y');
		$this->assertTrue($var->active);
		$var->active = null;
		$this->assertFalse($var->active);
		$var->setActive('TRue');
		$this->assertTrue($var->active);
		$var->setActive('truth');
		$this->assertFalse($var->active);
		$var->setActive(true);
		$this->assertTrue($var->active);
	}
	public function testLabels() {
		$var = new ELSWAK_Boolean;
		$this->assertEquals('no', $var->label);
		$var->value = 'x';
		$this->assertEquals('yes', ''.$var);
		$var->active = 'nope';
		$this->assertEquals('no', "$var");
		$this->assertEquals('TRUE', ELSWAK_Boolean::booleanAsString(true));
		$this->assertEquals('False', ELSWAK_Boolean::booleanAsString('qwer', 'True', 'False'));
	}
}
class ELSWAK_BooleanTest_Dummy
	extends ELSWAK_Object {
	protected $active;
	
	public function __construct($active = 'yes') {
		$this->active = new ELSWAK_Boolean($active);
	}
	public function setActive($value = 'yes') {
		$this->active->setValue($value);
		return $this;
	}
	public function active() {
		return $this->active->value();
	}
}