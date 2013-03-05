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
	
	public function testReset() {
		$data = new ELSWAK_Array(array('asdf', 'qwer', 'zxcv'));
		$this->assertEquals(3, $data->count());
		$this->assertEquals(0, $data->clear()->count());
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
	public function testIteratorishMethods() {	
		$var = new ELSWAK_Array;
		$var->add('one')
			->add('two')
			->add('three')
			->add('four');
		
		$item = $var
			->rewind()
			->next()
			->next()
			->previous()
			->current();
		$this->assertEquals($var->item(1), $item);
		$this->assertEquals('two', $item);
		
		// skip well beyond the end
		$this->assertFalse($var->next()->next()->next()->next()->next()->current());
		
		$this->assertEquals('three', $var->skipToValue('three')->current());
		
		// skip to a non existent value
		$this->assertFalse($var->skipToValue('ASDF')->current());
		
		$this->assertEquals('two', $var->skipToValue('two')->current());
		
		// skip to a non existent key
		$this->assertFalse($var->skipToKey(5)->current());
		
		$this->assertEquals('four', $var->skipToValue('four')->current());
		
		$var->set('005', 'five');
		$this->assertEquals('five', $var->skipToKey('005')->current());
		$this->assertEquals('005', $var->keyForValue('five'));
	}
	
	
	
//!String Export
	/**
	 * @depends testAddUnamed
	 */
	public function testToString(ELSWAK_Array $var) {
		$this->assertEquals(json_encode($var, JSON_PRETTY_PRINT), $var);
		$this->assertEquals('Bertram, Some content that doesn’t have a key', $var->formattedList());
		$var = new ELSWAK_Array;
		$this->assertEmpty($var->formattedList());
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
	
	public function testPushPops() {
		$var = new ELSWAK_Array;
		$this->assertEmpty($var->item(1));
		
		$var->push('asdf');
		$var->push('qwer');
		$var->push('zxcv');
		
		$this->assertEquals('asdf', $var->first());
		$this->assertEquals('qwer', $var->item(1));
		$this->assertEquals('zxcv', $var->last());
		
		$this->assertEquals('zxcv', $var->pop());
		$this->assertEquals('qwer', $var->last());
		
		$this->assertEquals('asdf', $var->shift());
		$this->assertEquals('qwer', $var->first());
		
		$var->unshift('hjkl');
		$this->assertEquals('hjkl', $var->first());
		$this->assertEquals(2, $var->count());
	}

	public function testInsertion() {
		$var = new ELSWAK_Array;
		$var->add('one');
		$var->add('two', 'two');
		$var->add('three');
		$this->assertEquals('two', $var->item(1));
		
		$var->insert('TWO', 1);
		$this->assertEquals('TWO', $var->item(1));
		
		$var->insert('too', 2);
		$this->assertEquals(5, $var->count());
		$this->assertEquals('TWO', $var->item(1));
		$this->assertEquals('too', $var->item(2));
		$this->assertEquals('two', $var->item(3));
		
		$var->insert('six');
		$this->assertEquals(6, $var->count());
		$this->assertEquals('six', $var->last());
		
		$var->delete(4);
		$this->assertEquals(5, $var->count());
		$this->assertEquals('six', $var->last());
	}
	
	public function testDifferences() {
		$var1 = new ELSWAK_Array;
		$var2 = new ELSWAK_Array;
		
		$var1->add('asdf');
		$var2->add('asdf');
		$diff = $var1->differences($var2);
		$this->assertFalse($diff->hasDifferences);
		
		// add an item to the second (should show 1 addition in the diff)
		$var2->set('TWO', 'two');
		$diff = $var1->differences($var2);
		$this->assertTrue($diff->hasDifferences);
		$this->assertEquals(1, $diff->added->count());
		
		// compare the first to the second (should show 1 removal in the diff)
		$diff = $var2->differences($var1);
		$this->assertTrue($diff->hasDifferences);
		$this->assertEquals(1, $diff->removed->count());
		
		// add the item to the first
		$var1->set('TWO', 'two');
		$diff = $var1->differences($var2);
		$this->assertFalse($diff->hasDifferences);
		
		// add the same items out of order to each other (should show 2 moves in the diff)
		$var1->add('qwer');
		$var1->add('zxcv');
		$var2->add('zxcv');
		$var2->add('qwer');
		$diff = $var1->differences($var2);
		$this->assertTrue($diff->hasDifferences);
		$this->assertEquals(2, $diff->moved->count());
		$this->assertEquals($diff->moved[1], 2);
		$this->assertEquals($diff->moved[2], 1);
	}
	/**
	 * @expectedException ELSWAK_Array_InvalidComparison_Exception
	 */
	public function testInvalidDifferences() {
		$var1 = new ELSWAK_Array;
		$var2 = new ELSWAK_Validated_Array;
		
		// var1 should be able to compare to var2 but not vice versa
		$this->assertInstanceOf('ELSWAK_Collection_Differences', $var1->differences($var2));
		
		$var2->differences($var1);
	}
}