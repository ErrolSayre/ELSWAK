<?php
// Provides code coverage for the class loader dummy class. The file is named with a Z to ensure it is loaded after the class loader tests so they can make proper use of loading the dummy class.
class ELSWAK_ClassLoaderDummyTest
	extends PHPUnit\Framework\TestCase {
	public function testHello() {
		$var = new ELSWAK_ClassLoaderDummy;
		$this->assertEquals('Hello', $var->sayHello());
	}
}