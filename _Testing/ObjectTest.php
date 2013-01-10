<?php
class ELSWAK_ObjectTest
	extends PHPUnit_Framework_TestCase {
	
	public function testConstructor() {
		$var = new ELSWAK_ObjectTest_Person;
	}
	public function testConstructorWithImport() {
		$var = new ELSWAK_ObjectTest_Person(array(
			'first' => 'Errol',
			'last' => 'Jones',
			'name' => 'Leonidas McDermott',
			'ssn' => 123456789,
		));
		return $var;
	}
	/**
	 * @depends testConstructorWithImport
	 */
	public function testAccessors(ELSWAK_ObjectTest_Person $var) {
		$this->assertEquals('Errol', $var->first);
		$this->assertEquals('Jones', $var->getLast());
		$var->setFirst('Juan')->setLast('Jiminez');
		$this->assertEquals('Jiminez', $var->last);
		$this->assertEquals('Juan Jiminez', $var->name);
		$this->assertFalse($var->getMarried());
	}
	/**
	 * @expectedException ELSWAK_Object_ProtectedProperty_Exception
	 */
	public function testProtectedPropertyGetter() {
		$var = new ELSWAK_ObjectTest_Person;
		$var->ssn;
	}
	/**
	 * @expectedException ELSWAK_Object_ProtectedProperty_Exception
	 */
	public function testProtectedProperty() {
		$var = new ELSWAK_ObjectTest_Person;
		$var->age;
	}
	/**
	 * @expectedException ELSWAK_Object_NonexistentProperty_Exception
	 */
	public function testNonexistentProperty() {
		$var = new ELSWAK_ObjectTest_Person;
		$var->children;
	}
	public function testVirtualPropertyGetter() {
		$var = new ELSWAK_ObjectTest_Person;
		$var->description();
	}
	/**
	 * @depends testConstructorWithImport
	 */
	public function testExport(ELSWAK_ObjectTest_Person $var) {
		$array = $var->_export();
		$this->assertGreaterThan(0, $array);
		$this->assertEquals($array['first'], $var->first);
	}
	/**
	 * @depends testConstructorWithImport
	 */
	public function testJson(ELSWAK_ObjectTest_Person $var) {
		$json = json_encode($var);
		$this->assertEquals($json, json_encode($var->_export));
	}
	/**
	 * @depends testConstructorWithImport
	 */
	public function testStringMethods(ELSWAK_ObjectTest_Person $var) {
		$var2 = new ELSWAK_ObjectTest_Person($var);
		$this->assertEquals($var, "$var2");
	}
	/**
	 * @expectedException ELSWAK_Object_ProtectedProperty_Exception
	 */
	public function testPrivatePropertyAccessors() {
		$var = new ELSWAK_ObjectTest_Person;
		try {
			$var->_viewCount = 5;
		} catch (Exception $e) {}
		$var->_viewCount(3);
	}
	public function testMethodExists() {
		$var = new ELSWAK_ObjectTest_Person;
		$this->assertTrue($var->_methodExists('setSSN'));
		$var2 = new ELSWAK_ObjectTest_Dummy;
		$this->assertTrue($var2->_methodExists('emptymethod'));
		$this->assertFalse($var2->_methodExists('happyMethod'));
	}
}

class ELSWAK_ObjectTest_Person
	extends ELSWAK_Object {
	
	protected $first;
	protected $last;
	protected $ssn;
	protected $age;
	
	private $_viewCount;
	
	public function getLast() {
		return $this->last;
	}
	public function name() {
		return $this->first.' '.$this->last;
	}
	public function setSSN($value) {
		$this->ssn = intval($value);
		return $this;
	}
	protected function getSSN() {
		return null;
	}
	protected function age() {
		return null;
	}
	
	public function getDescription() {
		return $this->name().' ('.$this->age.')';
	}
	public function married() {
		return false;
	}
	
	protected function set_viewCount($value) {
		return $this;
	}
	
	public function __toString() {
		++$this->_viewCount;
		return parent::__toString();
	}
}
class ELSWAK_ObjectTest_Dummy
	extends ELSWAK_Object {
	public function emptyMethod() {
		return null;
	}
}