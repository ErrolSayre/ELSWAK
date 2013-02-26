<?php
class ELSWAK_HTTP_ResponseTest
	extends PHPUnit_Framework_TestCase {

	public function testResponse() {
		// mock out the $_SEVER global
		global $_SERVER;
		$_SERVER = array();
		$_SERVER['HTTP_HOST'] = 'www.apple.com';
		$_SERVER['PHP_SELF'] = '/imac/features/coolstuff/index.php';
		
		// create the response
		$response = new ELSWAK_HTTP_Response;
		$response->setRedirect('https://www.apple.com/imac/');
		
		// setup a bunch of garbage from the old test
		$response->addContent('<h1>Welcome to Moe’s!</h1>'.LF);
		$response->addContent($response->serverUri().BR.LF);
		$response->addContent($response->applicationPath().BR.LF);
		$response->addMessage('The thingamajig didn’t work like a who’s-a-what’s-it');
		$response->addMessage('User not authenticated');
		$response->addMessage('User authentication form displayed.');
		$response->setStatus('Looking Good');
		$response->setHeader('Gillibush', 'Ferriwinkle');
		$response->setHeader('Jonny-Cab', 'To the Moon');
		$response->setContentType();
		
		
		
		// write the response to a file
		$path = pathinfo(__FILE__, PATHINFO_DIRNAME).'/ResponseTest.';
		file_put_contents($path.'actual.txt', $response->debugOutput());
		
		// compare the written response to the expected
		$this->assertFileEquals($path.'expected.txt', $path.'actual.txt');
	}
}