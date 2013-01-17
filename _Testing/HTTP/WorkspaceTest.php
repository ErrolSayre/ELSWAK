<?php
class ELSWAK_HTTP_WorkspaceTest
	extends PHPUnit_Framework_TestCase {
	
	public function testConstructor() {
		$var = new ELSWAK_HTTP_Workspace;
	}
	public function testHeaders() {
		$var = new ELSWAK_HTTP_Workspace;
		$var->addHeader('Location', 'https://www.apple.com');
		$var->setHeader('Location', 'https://www.olemiss.edu');
		$this->assertEquals('https://www.olemiss.edu', $var->header('Location'));
		$this->assertEquals(1, count($var->header()));
		$this->assertEquals(count($var->header()), $var->headers->count());
		$this->assertFalse($var->hasHeader('X-Purpose'));
		$var->headerForKey('X-Purpose');
		$var->removeHeader('Location');
	}
	public function testContent() {
		$var = new ELSWAK_HTTP_Workspace;
		$var->addContent('Hello world!');
		$var->setContent('Halo welt!', 'Content-1');
		$this->assertEquals('Halo welt!', $var->content('Content-1'));
		$this->assertTrue($var->hasContent('Content-1'));
		$this->assertEquals($var->contentForKey('Content-1'), $var->removeContent('Content-1'));
		$this->assertEquals($var->hasContentForKey('Content-1'), $var->hasContent());
	}
	public function testMetadata() {
		$var = new ELSWAK_HTTP_Workspace;
		$var->metadata->add('Errol', 'Author');
		$var->metadata->set('Author', 'John');
		$var->metadata->add('this is an inocuous keyword that should just go into the list of items');
		$this->assertEquals(2, $var->metadata->count());
	}
}