<?php
class ELSWAK_DictionaryTest
	extends PHPUnit_Framework_TestCase {
		
	public function testConstructor() {
		$var = new ELSWAK_Dictionary;
		$this->assertEquals(0, $var->count());
		return $var;
	}
	
	public function testImport() {
		$var = new ELSWAK_Dictionary(array('name' => 'Bertram'));
		$this->assertEquals('Bertram', $var->valueForKey('name'));
		$this->assertNull($var->valueForKey('First'));
	}
	
	public function testAddName() {
		$var = new ELSWAK_Dictionary;
		$var->add('Bertram', 'name');
		$this->assertEquals('Bertram', $var->get('name'));
		$this->assertEquals($var->get('name'), $var->valueForKey('name'));
		$this->assertEquals('Bertram', $var->valueForKeyWithException('name'));
		return $var;
	}
	
	/**
	 * @depends testAddName
	 */
	public function testCount(ELSWAK_Dictionary $var) {
		$this->assertEquals(1, $var->count);
		$this->assertEquals($var->count, $var->count());
		return $var;
	}
	
	/**
	 * @depends testAddName
	 */
	public function testAddUnamed(ELSWAK_Dictionary $var) {
		$key = $var->uniqueKeyForValue();
		$value = 'Some content that doesn’t have a key';
		$var->add($value);
		$this->assertEquals($value, $var->get($key));
		// add another blank record
		$var->add('ASDF');
		$var->remove($key);
		$var->add('ASDF');
		// remove the items to cause a collision
		$var->remove('Item-010');
		$var->remove('Item-1');
		$key = $var->uniqueKeyForValue();
		$value = 'Some content that doesn’t have a key';
		$var->add($value);
		$this->assertEquals($value, $var->get($key));
		return $var;
	}
	
	/**
	 * @depends testAddUnamed
	 */
	public function testGet(ELSWAK_Dictionary $var) {
		$data = $var->get();
		$this->assertGreaterThan(0, count($data));
		$this->assertEquals(count($data), $var->count);
		return $var;
	}
	
	
	
//!Invalid Keys
	public function testInvalidKeyTest() {
		$var = new ELSWAK_Dictionary;
		$this->assertFalse($var->has('Wonkies'));
	}
	
	/**
	 * @expectedException ELSWAK_Dictionary_InvalidKey_Exception
	 */
	public function testInvalidKeyAccess() {
		$var = new ELSWAK_Dictionary;
		$var->valueForKeyWithException('MONKEYS');
	}
	
	/**
	 * @expectedException ELSWAK_Dictionary_InvalidKey_Exception
	 */
	public function testInvalidKeyRemoval() {
		$var = new ELSWAK_Dictionary;
		$var->add('ASDF', 'QWER');
		$var->removeValueForKeyWithException('QWER');
		$var->removeValueForKeyWithException('qwer');
	}
	
	
	
//!Store Methods
	/**
	 * @expectedException ELSWAK_Object_ProtectedMethod_Exception
	 */
	public function testInvalidStoreSet() {
		$var = new ELSWAK_Dictionary();
		$var->setStore(array());
	}
	
	
	
//!Iteration
	/**
	 * @depends testAddUnamed
	 */
	public function testIteration(ELSWAK_Dictionary $var) {
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
	public function testToString(ELSWAK_Dictionary $var) {
		$this->assertEquals(json_encode($var->store, JSON_PRETTY_PRINT), $var);
	}
	
	public function testSortingAndJSONExport() {
		$var = new ELSWAK_Dictionary;
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
}