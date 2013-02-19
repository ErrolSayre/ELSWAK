<?php
class ELSWAK_ArrayTest
	extends PHPUnit_Framework_TestCase {

	public function testConstructor() {
		$var = new ELSWAK_Array;
		$this->assertEquals(0, $var->count());
		return $var;
	}
	
	public function testImport() {
		$var = new ELSWAK_Array(array('name' => 'Bertram'));
		$this->assertEquals('Bertram', $var->valueForKey('name'));
		$this->assertNull($var->valueForKey('First'));
		$object = new ELSWAK_Array(array('first' => 'Frank', 'last' => 'Oz'));
		$this->assertEquals('Frank', $object->first);
		// import a traversable object...
		$data = new ELSWAK_Array($object);
		$this->assertGreaterThan(1, $data->count());
	}
	
	public function testAddName() {
		$var = new ELSWAK_Array;
		$var->add('Bertram', 'name');
		$this->assertEquals('Bertram', $var->get('name'));
		$this->assertEquals($var->get('name'), $var->valueForKey('name'));
		$this->assertEquals('Bertram', $var->valueForKeyWithException('name'));
		return $var;
	}
	
	/**
	 * @depends testAddName
	 */
	public function testAddUnamed(ELSWAK_Array $var) {
		$value = 'Some content that doesn’t have a key';
		$var->add($value);
		// ensure the value is the last key added
		$data = $var->store();
		$test = array_pop($data);
		$this->assertEquals($value, $test);
		return $var;
	}
	
	public function testGet() {
		$var = new ELSWAK_Array;
		$var->add('ASDF');
		$this->assertEquals('ASDF', $var->get(0));
	}
	
	public function testArrayAndMagicAccessors() {
		// test accessing items with array and object notation, as well as foreach support
		$data = new ELSWAK_Array;
		$data['first'] = 'Puintus';
		$data[] = 'An item';
		$data['last'] = 'Vorenus';
		$this->assertEquals(array('first', 0, 'last'), $data->keys());
		$this->assertEquals('Vorenus', $data->last);
		$data->monster = 'Dracula';
		$this->assertEquals('Dracula', $data->get('monster'));
		$this->assertTrue($data->offsetExists('monster'));
		unset($data['first']);
		$this->assertEquals('Dracula', $data->offsetGet('monster'));
	}
	
	
	
//!Invalid Keys
	public function testInvalidKeyTest() {
		$var = new ELSWAK_Array;
		$this->assertFalse($var->has('Wonkies'));
	}
	
	/**
	 * @expectedException ELSWAK_Array_InvalidKey_Exception
	 */
	public function testInvalidKeyAccess() {
		$var = new ELSWAK_Array;
		$var->valueForKeyWithException('MONKEYS');
	}
	
	/**
	 * @expectedException ELSWAK_Array_InvalidKey_Exception
	 */
	public function testInvalidKeyRemoval() {
		$var = new ELSWAK_Array;
		$var->add('ASDF', 'QWER');
		$var->removeValueForKeyWithException('QWER');
		$var->removeValueForKeyWithException('qwer');
	}
	
	
	
//!Iteration
	/**
	 * @depends testAddUnamed
	 */
	public function testIteration(ELSWAK_Array $var) {
		$run1 = '';
		$run2 = '';
		foreach ($var as $key => $value) {
			$run1 .= $key.':'.$value.';';
		}
		$var->key();
		foreach ($var as $key => $value) {
			$run2 .= $key.':'.$value.';';
		}
		$this->assertNotNull($run1);
		$this->assertNotNull($run2);
		$this->assertEquals($run1, $run2);
	}
	
	
	
//!String Export
	/**
	 * @depends testAddUnamed
	 */
	public function testToString(ELSWAK_Array $var) {
		$this->assertEquals(json_encode($var, JSON_PRETTY_PRINT), $var);
	}
	
	public function testSortingAndJSONExport() {
		$var = new ELSWAK_Array;
		$var->set('asdf', 'zxcv');
		$var->set('qwer', 'asdf');
		$var->set('zxcv', 'qwer');
		
		// sort and compare the JSON encoding to a known sorted item...
		$var->sort();
		$this->assertEquals(
			json_encode(
				array(
					'qwer' => 'asdf',
					'zxcv' => 'qwer',
					'asdf' => 'zxcv',
				),
				JSON_FORCE_OBJECT
			),
			$var->toJSON()
		);
		// reverse sort and compare the JSON encoding to a known sorted item...
		$var->sortByValue(true);
		$this->assertEquals(
			json_encode(
				array(
					'asdf' => 'zxcv',
					'zxcv' => 'qwer',
					'qwer' => 'asdf',
				),
				JSON_FORCE_OBJECT
			),
			$var->toJSON()
		);
		// key sort and compare the JSON encoding to a known sorted item...
		$var->sortByKey();
		$this->assertEquals(
			json_encode(
				array(
					'asdf' => 'zxcv',
					'qwer' => 'asdf',
					'zxcv' => 'qwer',
				),
				JSON_FORCE_OBJECT
			),
			$var->toJSON()
		);
		// reverse key sort and compare the JSON encoding to a known sorted item...
		$var->sortByKey(true);
		$this->assertEquals(
			json_encode(
				array(
					'zxcv' => 'qwer',
					'qwer' => 'asdf',
					'asdf' => 'zxcv',
				),
				JSON_FORCE_OBJECT
			),
			$var->toJSON()
		);
	}
	
	public function testItemSearch() {
		$var = new ELSWAK_Array(array(
			'020' => 'Spring Semester',
			'404' => 'Not Found',
			'HELO' => 'Not BSG',
		));
		$this->assertEquals('HELO', $var->parseItem('BSG'));
		$this->assertEquals('Not Found', $var->parseItem(404, true));
		$this->assertNull($var->parseItem(200));
	}
}