<?php
require_once dirname(dirname(dirname(__FILE__))).'/setup-environment.php';

class ELSWAK_JSON_Data_CoderTest
	extends PHPUnit_Framework_TestCase {
	
	public function testConstructor() {
		$coder = new ELSWAK_JSON_Data_Coder;
		$this->assertInstanceOf('ELSWAK_JSON_Data_Coder', $coder);
		return $coder;
	}
	/**
	 * @depends testConstructor
	 */
	public function testEncode(ELSWAK_JSON_Data_Coder $coder) {
		$array = [
			'name' => 'Test E. Tester',
			'employed' => true,
			'working' => false,
		];
		$items = [];
		$items[] = $coder->encode($array);
		$this->assertEquals(json_encode($array), $items[0]);
		$object = new ELSWAK_User( [
			'account' => 'tester',
			'displayName' => 'Test E. Tester',
		] );
		$items[] = $coder->encode($object);
		$this->assertEquals(json_encode($object), $items[1]);
		return $items;
	}
	/**
	 * @depends testEncode
	 */
	public function testDecode(array $items) {
		$coder = new ELSWAK_JSON_Data_Coder;
		foreach ($items as $json) {
			$decoded = json_decode($json);
			if ($decoded !== null) {
				$this->assertEquals($decoded, $coder->decode($json));
			}
		}
	}
	public function testDecodeOptions() {
		$coder = new ELSWAK_JSON_Data_Coder;
		$json = '{"name":"Test E. Tester","employed":true,"working":false}';
		$this->assertEquals(
			json_decode($json, true, 10, 0),
			$coder->decode($json, [
				'assoc' => true,
				'depth' => 10,
				'options' => 0
			] )
		);
	}
}