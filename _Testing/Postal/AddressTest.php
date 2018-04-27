<?php
class ELSWAK_Postal_AddressTest
	extends PHPUnit\Framework\TestCase {

	public function testAddress() {
		$address = new ELSWAK_Postal_Address;
		$this->assertInstanceOf('ELSWAK_Postal_Address', $address);
		$this->assertEmpty($address->cityStateZipLine);
		
		$address = new ELSWAK_Postal_Address(array(
			'line1' => '30 Sorority Row',
			'line2' => null,
			'city' => 'University',
			'state' => 'MS',
			'zip' => 38677,
			'country' => 'U.S.A'
		));
		$this->assertInstanceOf('ELSWAK_Postal_Address', $address);
		$this->assertEquals(1, $address->lineCount);
		$this->assertEquals('30 Sorority Row, University, MS 38677, U.S.A', $address->address('single-line'));
		
		$text = 'Office of Research'.LF
			.'125 Old Chemistry'.LF
			.'University, MS 38677';
		$address = $address->parseAddress($text);
		$this->assertInstanceOf('ELSWAK_Postal_Address', $address);
		$this->assertEquals($text, $address->address);
		$this->assertEquals(38677, $address->zipCode);
		$this->assertEquals(3, count($address->address('array')));
		
		$address = $address->parseAddress( 'Kincannon 606, University, MS 38677 ' );
		$this->assertInstanceOf( 'ELSWAK_Postal_Address', $address );
		$this->assertEquals( 'Kincannon 606', $address->line1 );
		$this->assertEquals( 'University', $address->city );
		$this->assertEquals( 38677, $address->postal );
		
		$text = 'Office of Information Technology 100 Powers Hall University Mississippi 38677-1848';
		$address = $address->parseAddress($text);
		$this->assertEquals('University, Mississippi 38677-1848', $address->cityStateZipLine);
		
		$text = 'Office of Information Technology 100 Powers Hall University';
		$address = $address->parseAddress($text);
		$this->assertEquals('University', $address->city);
		
		$address = new ELSWAK_Postal_Address('Marty McFly', '9303 Lyon Drive', 'Hill Valley', 'California', 91331, 'U.S.A');
		$this->assertEquals('9303 Lyon Drive', $address->line2);
		$address->state = null;
		$this->assertEquals('Hill Valley 91331', $address->cityStateZipLine);
		$this->assertEquals(91331, $address->postalCode);
		$address->postal = null;
		$this->assertEquals('Hill Valley', $address->cityStateZipLine);
		$address->state = 'CA';
		$this->assertEquals('Hill Valley, CA', $address->cityStateZipLine);
		
		// do some obscure tests for coverage
		$this->assertEquals(count($address->lines), $address->linesCopy->count());
		$this->assertEmpty($address->line(3));
		$address->line1 = null;
		$this->assertEmpty($address->line1);
		$address->line2 = null;
		$this->assertEmpty($address->line2);
		
		$address = new ELSWAK_Postal_Address;
		$address->line1 = 'Errol Sayre';
		$address->line2 = '217 Powers Hall';
		$this->assertEquals('Errol Sayre, 217 Powers Hall', "$address");
		
		$address->setStates(new ELSWAK_Array(array(
			'Burgenland',
			'Kärnten',
			'Niederösterreich',
			'Oberösterreich',
			'Salzburg',
			'Steiermark',
			'Tirol',
			'Vorarlberg',
			'Vienna',
		)));
	}
}