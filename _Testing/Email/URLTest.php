<?php
class ELSWAK_Email_URLTest
	extends PHPUnit_Framework_TestCase {
	
	public function testConstructor() {
		$url = new ELSWAK_Email_URL;
		$this->assertInstanceOf('ELSWAK_Email_URL', $url);
	}
	public function testAccessors() {
		$url = new ELSWAK_Email_URL;
		$url->address = 'steve@microsoft.com';
		$url->query = 'subject=Windows!';
		// the scheme should always be included on email URLs
		$this->assertEquals('mailto:steve@microsoft.com?subject=Windows%21', $url->url);
	}
}