<?php
class ELSWAK_URI_FactoryTest
	extends PHPUnit\Framework\TestCase {
	
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
				"https://username:P%40ssw0rd@server.domain.tld/product/category/app/entity(value='301-4014',scheme_id='CATEGORY_TYPE',scheme_agency_id='ID_111')",

			),
			'ELSWAK_Email_URL' => array(
				'mailto:steve@apple.com',
				'mailto:errol@localhost',
			),
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
	/**
	 * Test factories that build against $_SERVER
	 *
	 * Since these tests run from the CLI, the $_SERVER global is not
	 * generally set. Although the worker methods all accept an incoming
	 * array which should mimick $_SERVER, for code coverage we'll spoof
	 * the $_SERVER global.
	 */
	public function testServerBasedFactories() {
		global $_SERVER;
		$uris = array(
			'https://asfd.qwer.com:8443/path/to/application/' => array(
				'SERVER_NAME' => 'asfd.qwer.com',
				'HTTPS' => 'on',
				'SERVER_PORT' => 8443,
				'PHP_SELF' => '/path/to/application/default.php',
			),
		);
		foreach ($uris as $expected => $data) {
			$_SERVER = $data;
			$url = ELSWAK_URI_Factory::applicationURLFromServerGlobal();
			$this->assertEquals($expected, $url->url);
		}



		$uris = array(
			'http://asfd.qwer.com/path/to/application/arguments/to/app/with/target.format?a=b&c=d#anchor' => array(
				'SERVER_NAME' => 'asfd.qwer.com',
				'SERVER_PORT' => 80,
				'REQUEST_URI' => '/path/to/application/arguments/to/app/with/target.format?a=b&c=d#anchor',
			),
		);
		foreach ($uris as $expected => $data) {
			$_SERVER = $data;
			$url = ELSWAK_URI_Factory::urlFromServerGlobal();
			$this->assertEquals($expected, $url->url);
		}
		
		// for coverage, test the base url method also
		$_SERVER = array(
			'SERVER_NAME' => 'asfd.qwer.com',
			'HTTPS' => 'on',
			'SERVER_PORT' => 8443,
			'PHP_SELF' => '/path/to/application/default.php',
		);
		$url = ELSWAK_URI_Factory::baseURLFromServerGlobal();
		$this->assertEquals('https://asfd.qwer.com:8443', $url->url);
	}
}