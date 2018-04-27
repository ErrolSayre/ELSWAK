<?php
class ELSWAK_URLTest
	extends PHPUnit\Framework\TestCase {
	
	public function testConstructor() {
		$uri = new ELSWAK_URL;
		$this->assertInstanceOf('ELSWAK_URL', $uri);
		$this->assertEquals('', $uri->url());
	}
}