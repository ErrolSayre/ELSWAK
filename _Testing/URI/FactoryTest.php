<?php
class ELSWAK_URI_FactoryTest
	extends PHPUnit_Framework_TestCase {
	
	public function testURLParsing() {
		// test strings that should and shouldn't work...
		$uris = array(
			'ELSWAK_Authoritative_URL' => array(
				'/app/module/component/123/edit',
				'ssh://user:password@apple.com',
			),
			'ELSWAK_HTTP_URL' => array(
				'http://www.google.com',
				'http://www.yahoo.com/',
				'http://www.apple.com/ipad-mini/overview/#video-ipad-mini-features',
				'http://www.olemiss.edu/?asdf=qwer',
				'http://www.olemiss.edu/?asdf=qwer&zxcv=hjkl',
				'http://www.olemiss.edu/?search=Ole+Miss',
				'http://www.google.com#search' => 'http://www.google.com/#search',
				'https://apple.com?' => 'https://apple.com',
				'http://apple.com/?#frank' => 'http://apple.com/#frank',
				'http://olemiss.edu/?search=Ole Miss' => 'http://olemiss.edu/?search=Ole+Miss',
				'https://localhost:8080/gallery/photos.json?date=20130101',
				'http://u%24er:p%40ssw0rd@localho%24t:202O/Ole+Miss/Rebel%24.htm%20l?first+name=Errol&display=Mr. Errol#%24dmin' => 'http://u%24er:p%40ssw0rd@localho%24t:202/Ole+Miss/Rebel%24.htm%20l?first+name=Errol&display=Mr.+Errol#%24dmin',
			),
/*
			'ELSWAK_Email_URL' => array(
				'mailto:steve@apple.com',
				'mailto:errol@localhost',
			),
*/
			'ELSWAK_URN' => array(
				'urn:animals:camel:head:nose#leftNostril',
			),
		);
		foreach ($uris as $class => $strings) {
			foreach ($strings as $fail => $string) {
				$uri = ELSWAK_URI_Factory::uriForString($string);
				$this->assertInstanceOf($class, $uri);
				$this->assertEquals($string, $uri->uri());
				if (is_string($fail)) {
					// perform this test forcing the use of the __toString method to get coverage there as well
					$this->assertNotEquals($fail, "$uri");
				}
			}
		}
	}
}