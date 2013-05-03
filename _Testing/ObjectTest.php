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
	
	public function testArrayAccessors() {
		$var = new ELSWAK_ObjectTest_Person();
		$var['first'] = 'James';
		$var['last'] = 'McCoy';
		$this->assertEquals('James', $var['first']);
		$var['last'] = null;
		$this->assertEquals(null, $var['last']);
		unset($var['first']);
		$this->assertEquals(null, $var['first']);
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

		// age and ssn are protected and should not be exported
		// mother is a lazy-init property and should not be exported until actually used
		$var = new ELSWAK_ObjectTest_Person(array('first' => 'James', 'last' => 'Dean'));
		$this->assertEquals('{"last":"Dean","first":"James"}', json_encode($var));
		
		// now set the mother's name and check the export again
		$var->mother->first = 'Judy';
		$var->mother->last  = 'Dench';
		$this->assertEquals('{"last":"Dean","first":"James","mother":{"last":"Dench","first":"Judy"}}', json_encode($var));
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
	public function testComparison() {
		$person1 = new ELSWAK_ObjectTest_Person;
		$person2 = new ELSWAK_ObjectTest_Person;
		
		$this->assertTrue($person1->isEqualTo($person2));
		
		$person1->first = 'Jane';
		$person2->first = 'John';
		$this->assertTrue($person1->isLessThan($person2));
		
		$person1->last = 'Smith';
		$person2->last = 'Doe';
		$this->assertTrue($person1->isGreaterThan($person2));
	}
}

class ELSWAK_ObjectTest_Person
	extends ELSWAK_Object {
	
	protected $last;
	protected $first;
	protected $age;
	protected $ssn;
	protected $mother;
	
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



	public function setMother(ELSWAK_ObjectTest_Person $person) {
		$this->mother = $person;
		return $this;
	}
	// lazy init the mother
	public function mother() {
		if (!$this->mother) {
			$this->setMother(new ELSWAK_ObjectTest_Person);
		}
		return $this->mother;
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