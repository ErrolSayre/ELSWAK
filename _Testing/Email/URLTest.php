<?php
class ELSWAK_Email_URLTest
	extends PHPUnit_Framework_TestCase {
	
	public function testConstructor() {
		$url = new ELSWAK_Email_URL;
		$this->assertInstanceOf('ELSWAK_Email_URL', $url);
	}
}