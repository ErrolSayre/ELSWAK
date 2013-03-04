<?php
class ELSWAK_Collection_DifferencesTest
	extends PHPUnit_Framework_TestCase {

	public function testDifferences() {
		$diff = new ELSWAK_Collection_Differences;
		$this->assertFalse($diff->hasDifferences);
		
		$diff->same->add('QWER');
		$this->assertFalse($diff->hasDifferences);
		
		$diff->changed->add('MONKEYS');
		$this->assertTrue($diff->hasDifferences);
		
		$diff->changed->clear();
		$this->assertFalse($diff->hasDifferences);
	}
}