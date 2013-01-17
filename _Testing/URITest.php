<?php
class ELSWAK_URITest
	extends PHPUnit_Framework_TestCase {
	
	public function testConstructor() {
		$uri = new ELSWAK_URI;
		$this->assertInstanceOf('ELSWAK_URI', $uri);
		$uri = new ELSWAK_URI(array(
			'scheme' => 'urn',
			'hierarchy' => 'animals:cats:house:abner',
			'query' => 'birthdate=20060401',
			'fragment' => 'calendar',
		));
		$this->assertInstanceOf('ELSWAK_URI', $uri);
		return $uri;
	}
	
	/**
	 * @depends testConstructor
	 */
	public function testAccessors(ELSWAK_URI $uri) {
		$this->assertEquals('urn:animals:cats:house:abner?birthdate=20060401#calendar', "$uri");
		$uri = new ELSWAK_URI;
		$this->assertFalse($uri->hasQuery());
		$url->query = 'sort=name&descending=true';
		$this->assertEquals('sort=name&descending=true', $url->query);
		$this->assertFalse($uri->hasFragment());
	}
}