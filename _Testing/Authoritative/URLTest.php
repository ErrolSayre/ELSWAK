<?php
class ELSWAK_Authoritative_URLTest
	extends PHPUnit_Framework_TestCase {
	
	public function testConstructor() {
		$url = new ELSWAK_Authoritative_URL;
		$this->assertInstanceOf('ELSWAK_Authoritative_URL', $url);
	}
	public function testAccessors() {
		$url = new ELSWAK_Authoritative_URL(array(
			'scheme' => 'ssh',
			'host' => 'localhost',
		));
		$this->assertEquals('ssh://localhost', $url->url());
		$url->user = 'errol';
		$this->assertEquals('ssh://errol@localhost', "$url");
		$this->assertEquals('ssh://errol@localhost', $url->serverURI());
		
		// set a password
		$url->password = '1234-Spaceballs';
		$this->assertEquals('ssh://errol:1234-Spaceballs@localhost', (string) $url);
		
		// add a special character and see the encoded form
		$url->password = 'sp@c3b@ll$';
		$this->assertNotEquals('ssh://errol:sp@c3b@ll$@localhost', $url->url);
		
		// change to https to provide a path
		$url->scheme = 'https';
		$url->path = '/Users/errol/Desktop';
		$this->assertEquals('https://errol:sp%40c3b%40ll%24@localhost/Users/errol/Desktop', $url->url);
		$this->assertEquals('https://errol:sp%40c3b%40ll%24@localhost', $url->serverUri);
		// test relative paths... this should be "corrected" since the path must start with a /
		$url->path = '~/Desktop';
		$url->query = 'sort=name&descending=true';
		$url->fragment = 'ELSWAK';
		$this->assertEquals('https://errol:sp%40c3b%40ll%24@localhost/~/Desktop?sort=name&descending=true#ELSWAK', $url->url);
		// test the special case for wiping out path components
		$url->setPathComponents();
		$this->assertEquals('https://errol:sp%40c3b%40ll%24@localhost/Desktop?sort=name&descending=true#ELSWAK', $url->url);
		// test wiping out the path altogether
		$url->setPath();
		$this->assertEquals('https://errol:sp%40c3b%40ll%24@localhost/?sort=name&descending=true#ELSWAK', $url->url);
		
		// create a "constructed" url scheme
		$url = new ELSWAK_Authoritative_URL;
		$this->assertFalse($url->hasAuthority);
		$this->assertEmpty($url->url);
		$url->scheme = 'mysql';
		$url->user = 'pma';
		$url->password = 'PresidentSkroob';
		$url->host = 'localhost';
		$url->port = 3306;
		$this->assertEquals('mysql://pma:PresidentSkroob@localhost:3306', $url->url);
		// set the port to something erroneous (note, this is the letter o)
		$url->port = '33O6';
		$this->assertEquals('mysql://pma:PresidentSkroob@localhost:33', $url->url);
		$url->port = '';
		$url->path = '/tmp/mysql.sock';
		$this->assertEquals('mysql://pma:PresidentSkroob@localhost/tmp/mysql.sock', $url->url);
		$this->assertEquals('mysql://pma:PresidentSkroob@localhost', $url->serverURI);
	}
	
	public function testTrailingSlash() {
		$url = new ELSWAK_Authoritative_URL(array(
			'scheme' => 'http',
			'host' => 'apple.com',
			'path' => '/'
		));
		$this->assertEquals('http://apple.com/', $url->url);
		
		$url = new ELSWAK_Authoritative_URL(array(
			'scheme' => 'http',
			'host' => 'apple.com',
		));
		$this->assertEquals('http://apple.com', $url->url);
	}
}