<?php
class ELSWAK_URLTest
	extends PHPUnit_Framework_TestCase {
	
	public function testConstructor() {
		$url = new ELSWAK_URL;
		$this->assertInstanceOf('ELSWAK_URL', $url);
	}
	
	public function testURLParsing() {
		// test strings that should and shouldn't work...
		$strings = array(
			'/app/module/component/123/edit',
			'http://www.google.com',
			'http://www.yahoo.com/',
			'http://www.apple.com/ipad-mini/overview/#video-ipad-mini-features',
			'http://www.olemiss.edu/?asdf=qwer',
			'http://www.olemiss.edu/?asdf=qwer&zxcv=hjkl',
			'http://www.olemiss.edu/?search=Ole+Miss',
			'http://www.google.com#search' => 'http://www.google.com/#search',
			'http://apple.com?' => 'http://apple.com',
			'http://apple.com/?#frank' => 'http://apple.com/#frank',
			'http://olemiss.edu/?search=Ole Miss' => 'http://olemiss.edu/?search=Ole+Miss',
			'ssh://user:password@apple.com',
			'https://localhost:8080/gallery/photos.json?date=20130101',
			'mailto:steve@apple.com',
			'mailto:errol@localhost',
		);
		foreach ($strings as $fail => $string) {
			$url = ELSWAK_URL::urlForString($string);
			$this->assertEquals($string, $url->url());
			if (is_string($fail)) {
				// perform this test forcing the use of the __toString method to get coverage there as well
				$this->assertNotEquals($fail, "$url");
			}
		}
	}
}