<?php
class ELSWAK_HTTP_URLTest
	extends PHPUnit\Framework\TestCase {
	
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
	
	public function testRelativeURLs() {
		$url = new ELSWAK_HTTP_URL(array(
			'scheme' => 'http',
			'host' => 'maps.google.com',
			'path' => '/maps/api/geocode/json',
			'query' => 'address=santa+cruz&components=country:ES&sensor=false',
		));
		$this->assertEquals('http://maps.google.com/maps/api/geocode/json?address=santa+cruz&components=country%3AES&sensor=false', $url->url);
		$this->assertEquals('/maps/api/geocode/json?address=santa+cruz&components=country%3AES&sensor=false', $url->url(true));
	}
}