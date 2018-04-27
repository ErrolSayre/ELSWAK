<?php
class ELSWAK_HTTP_ResponseTest
	extends PHPUnit\Framework\TestCase {

	public function testResponse() {
		// mock out the $_SEVER global
		global $_SERVER;
		$_SERVER = array();
		$_SERVER['HTTP_HOST'] = 'www.apple.com';
		$_SERVER['PHP_SELF'] = '/imac/features/coolstuff/index.php';
		$_SERVER['REQUEST_TIME'] = 1234567890.1234;
		$_SERVER['SCRIPT_NAME'] = '';
		
		// create the response
		$response = new ELSWAK_HTTP_Response;
		$this->assertEquals( '/imac/features/coolstuff/', $response->applicationPath() );
		
		// set the canonical url
		$url = 'http://www.apple.com/imac/features/coolstuff/';
		$response->setCanonicalURL( $url );
		$response->setRedirect();
		$this->assertEquals( $url, (string) $response->canonicalURL() );
		$this->assertEquals( $url, $response->redirectURL() );
		
		$url = 'https://www.apple.com/imac/';
		$response->setRedirect( $url );
		$this->assertEquals( $url, $response->redirectURL() );
		
		$response->overrideApplicationPath('/ipad/features/index.php');
		$this->assertEquals('/ipad/features/', $response->applicationPath());
		
		// setup a bunch of garbage from the old test
		$response->addContent('<h1>Welcome to Moe’s!</h1>'.LF);
		$response->addContent('Server URI: '.$response->serverUri().BR.LF);
		$response->addContent('Application Path: '.$response->applicationPath().BR.LF);
		$response->addMessage('The thingamajig didn’t work like a who’s-a-what’s-it');
		$response->addMessage('# User not authenticated'.LF.'You’ll have to login.');
		$response->addMessage('User authentication form displayed.');
		$response->setStatus('Looking Good');
		$response->setHeader('Gillibush', 'Ferriwinkle');
		$response->setHeader('Jonny-Cab', 'To the Moon');
		$response->setContentType();
		
		// ensure the base url is tamper resistent
		$response->baseURL()->path = '/appletv';
		$this->assertEquals('/ipad/features/', $response->applicationPath());
		$url = $response->baseURL()->setPath('/appletv');
		$this->assertEquals('/appletv', $url->path());
		
		// write the response to a file
		$path = pathinfo(__FILE__, PATHINFO_DIRNAME).'/'.pathinfo(__FILE__, PATHINFO_FILENAME).'.';
		file_put_contents($path.'actual.txt', $response->debugOutput());
		
		// compare the written response to the expected
		$this->assertFileEquals($path.'expected.txt', $path.'actual.txt');
		
		return $response;
	}
	
	/**
	 * @depends testResponse
	 */
	public function testRenderFactory( ELSWAK_HTTP_Response $response ) {
		// use the factory to "render" this response down
		$rendered = $response->renderedResponse();
		
		// utilize the content count to assure items are different
		$this->assertNotEquals( $response->contentCount(), $rendered->contentCount() );
		$this->assertEquals( 1, $rendered->contentCount() );
		
		// write the rendered response to the file system to compare again
		$path = pathinfo(__FILE__, PATHINFO_DIRNAME).'/'.pathinfo(__FILE__, PATHINFO_FILENAME).'.';
		file_put_contents($path.'actual.txt', $rendered->debugOutput());
		
		// compare the written response to the expected
		$this->assertFileEquals($path.'expected.txt', $path.'actual.txt');
	}
}