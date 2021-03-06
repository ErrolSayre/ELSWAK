<?php
class ELSWAK_DictionaryTest
	extends PHPUnit\Framework\TestCase {
	
	public function testImport() {
		$var = new ELSWAK_Dictionary(array(
			'asdf',
			'qwer' => 'zxcv',
		));
		$this->assertEquals(2, $var->count());
	}
	
	public function testAddUnamed() {
		$var = new ELSWAK_Dictionary;
		$var->add('Bertram', 'name');
		$this->assertEquals('Bertram', $var->get('name'));
		$this->assertEquals($var->get('name'), $var->valueForKey('name'));
		$this->assertEquals('Bertram', $var->valueForKeyWithException('name'));
		
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
	
	public function testAddSet() {
		$var = new ELSWAK_Dictionary;
		$var->add('Asdf');
		$this->assertEquals('Asdf', $var->get('Item-1'));
		$var->set('Item-1', 'QWER');
		$this->assertEquals('QWER', $var->get('Item-1'));
	}
	
	public function testArrayAndMagicAccessors() {
		// test accessing items with array and object notation, as well as foreach support
		$data = new ELSWAK_Dictionary;
		$data['first'] = 'Puintus';
		$data[] = 'An item';
		$data['last'] = 'Vorenus';
		$this->assertEquals(array('first', 'Item-2', 'last'), $data->keys());
	}
}