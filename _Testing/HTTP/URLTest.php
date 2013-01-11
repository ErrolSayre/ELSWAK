<?php
class ELSWAK_HTTP_URLTest
	extends PHPUnit_Framework_TestCase {
	
	public function testConstructor() {
		$url = new ELSWAK_HTTP_URL;
		$this->assertInstanceOf('ELSWAK_HTTP_URL', $url);
	}
	
	public function testSchemeSetter() {
		$url = new ELSWAK_HTTP_URL;
		$url->scheme = 'HTTP';
		$this->assertEquals('http', $url->scheme);
		$url->host = 'apple.com';
		$this->assertEquals('http://apple.com', $url->url);
		$url->scheme = 'https';
		$this->assertEquals('https://apple.com', $url->url);
		$url->scheme = '';
		$this->assertEquals('//apple.com', $url->url);
		
		// ensure that php's parser can read the authority properly without a scheme
		$data = parse_url($url->url);
		$this->assertEquals($url->host, $data['host']);
	}
	
	/**
	 * @expectedException ELSWAK_HTTP_URL_InvalidScheme_Exception
	 */
	public function testSchemeException() {
		$url = new ELSWAK_HTTP_URL;
		$url->scheme = 'ftp';
	}
}