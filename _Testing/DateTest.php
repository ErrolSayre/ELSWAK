<?php
class ELSWAK_DateTest
	extends PHPUnit_Framework_TestCase {

	public function testDate() {
		$tests = array(
			    'ATOM' => '2013-11-11T11:11:11+00:00',
			  'COOKIE' => 'Monday, 11-Nov-2013 11:11:11 UTC',
			'DATETIME' => '2013-11-11 11:11:11',
			    'FULL' => 'Monday, November 11, 2013',
			'FULLTIME' => '11:11:11 am UTC',
			 'ISO8601' => '2013-11-11T11:11:11+0000',
			  'RFC822' => 'Mon, 11 Nov 13 11:11:11 +0000',
			  'RFC850' => 'Monday, 11-Nov-13 11:11:11 UTC',
			 'RFC1036' => 'Mon, 11 Nov 13 11:11:11 +0000',
			 'RFC1123' => 'Mon, 11 Nov 2013 11:11:11 +0000',
			 'RFC2822' => 'Mon, 11 Nov 2013 11:11:11 +0000',
			 'RFC3339' => '2013-11-11T11:11:11+00:00',
			     'RSS' => 'Mon, 11 Nov 2013 11:11:11 +0000',
			     'W3C' => '2013-11-11T11:11:11+00:00',
		);
		
		$date = new ELSWAK_Date('2013-11-11 11:11:11 UTC');
		
		foreach ($tests as $test => $expected) {
			$this->assertEquals($expected, $date->{$test}, 'Date format getter failed for '.$test.': '.$date->{$test});
		}
	}
}