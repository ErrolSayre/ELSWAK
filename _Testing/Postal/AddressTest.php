<?php
class ELSWAK_Postal_AddressTest
	extends PHPUnit_Framework_TestCase {

	public function testConstructor() {
		$address = new ELSWAK_Postal_Address;
		$this->assertInstanceOf('ELSWAK_Postal_Address', $address);
		
		$address = new ELSWAK_Postal_Address(array(
			'line1' => '30 Sorority Row',
			'line2' => null,
			'city' => 'University',
			'state' => 'MS',
			'postal' => '38677',
			'country' => 'U.S.A'
		));
		$this->assertInstanceOf('ELSWAK_Postal_Address', $address);
		$this->assertEquals(1, $address->lineCount);
		
		$text = 'Office of Research'.LF
			.'125 Old Chemistry'.LF
			.'University, MS 38677';
		$address = $address->parseAddress($text);
		$this->assertInstanceOf('ELSWAK_Postal_Address', $address);
		$this->assertEquals($text, $address->address);
		$this->assertEquals(38677, $address->zipCode);
	}
}