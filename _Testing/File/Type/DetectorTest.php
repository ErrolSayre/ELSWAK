<?php
class ELSWAK_File_Type_DetectorTest
	extends PHPUnit\Framework\TestCase {

	public function testDetector() {
		// construct a new detector
		$td = new ELSWAK_File_Type_Detector();
		
		$files = array (
			'george.xls',
			'main.cpp',
			'howdee.cif',
			'12.rar',
		);
		
		foreach ($files as $file) {
			$this->assertNotEquals('application/octet-stream', $td->typeFromFile($file));
		}
		$this->assertEquals('application/x-httpd-php', $td->typeFromFile(__FILE__));
		$this->assertEquals('application/octet-stream', $td->typeFromFile('asdf.qwex'));
	}
}