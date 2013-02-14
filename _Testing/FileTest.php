<?php
class ELSWAK_FileTest
	extends PHPUnit_Framework_TestCase {
	
	public function testFile() {
		// create a file for this file
		$file = new ELSWAK_File(__FILE__);
		$this->assertEquals('FileTest.php', $file->name);
		$this->assertEquals('php', $file->extension);
		$this->assertEquals('application/x-httpd-php', $file->type);
	}
}