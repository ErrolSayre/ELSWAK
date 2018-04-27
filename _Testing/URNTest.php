<?php
class ELSWAK_URNTest
	extends PHPUnit\Framework\TestCase {
	
	public function testConstructor() {
		$urn = new ELSWAK_URN;
		$this->assertInstanceOf('ELSWAK_URN', $urn);
		$urn = new ELSWAK_URN(array(
			'scheme' => 'urn',
			'hierarchy' => 'animals:cats:house:abner',
			'query' => 'birthdate=20060401',
			'fragment' => 'calendar',
		));
		$this->assertInstanceOf('ELSWAK_URN', $urn);
		return $urn;
	}
	
	/**
	 * @depends testConstructor
	 */
	public function testAccessors(ELSWAK_URN $urn) {
		$this->assertEquals('urn:animals:cats:house:abner?birthdate=20060401#calendar', $urn->urn());
		$this->assertTrue($urn->hasPath);
		$urn->setPathComponents();
		$this->assertFalse($urn->hasPath);
		$urn = new ELSWAK_URN;
		$this->assertFalse($urn->hasPath());
		$this->assertFalse($urn->hasQuery());
		$this->assertFalse($urn->hasFragment());
	}
}