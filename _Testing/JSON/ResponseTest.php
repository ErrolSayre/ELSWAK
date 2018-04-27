<?php
class ELSWAK_JSON_ResponseTest
	extends PHPUnit\Framework\TestCase {
	
	public function testOverrides() {
		$response = new ELSWAK_JSON_Response;
		$this->assertEmpty($response->body());
		$data = array('mark' => true);
		$response->setBody($data);
		$this->assertEquals($data, $response->body());
	}
	public function testSimpleResponse() {
		$response = new ELSWAK_JSON_Response;
		
		$response->addContent('item 1');
		$response->addContent('item 2');
		$response->addContent('first', 'firstName');
		$response->addContent('last', 'lastName');
		$response->addContent('{"firstName":"person","lastName":"1"}', 'json string 1');
		$response->addContent('{"firstName":"person","lastName":"2"}', 'json literal 1', 'json');
		$response->addContent(array('first' => 'Errol', 'last' => 'Sayre'), 'myself');
		
		// write the response to a file
		$path = pathinfo(__FILE__, PATHINFO_DIRNAME).'/'.pathinfo(__FILE__, PATHINFO_FILENAME).'-simple.';
		file_put_contents($path.'actual.txt', $response->debugOutput());
		
		// compare the written response to the expected
		$this->assertFileEquals($path.'expected.txt', $path.'actual.txt');
	}
}