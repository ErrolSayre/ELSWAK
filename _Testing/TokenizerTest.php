<?php
class ELSWAK_TokenizerTest
	extends PHPUnit\Framework\TestCase {

	public function testStandard() {
		$tokenizer = ELSWAK_Tokenizer::standardTokenizer();

		$tests = [
			'Hello world!'
				=> [ 'Hello', ' ', 'world', '!' ],
			'This’s the “thing” you \'wanted\': "iñtërnâtiônàlizætiøn".'
				=> [ 'This’s', ' ', 'the', ' ', '“thing”', ' ', 'you', ' ', "'wanted'", ':', ' ', '"iñtërnâtiônàlizætiøn"', '.' ],
			'“This is a sample of ‘smart quotes’.”'
				=> [ '“This', ' ', 'is', ' ', 'a', ' ', 'sample', ' ', 'of', ' ', '‘smart', ' ', 'quotes’', '.', '”' ],
			'Fancy Pants/The Other one'
				=> [ 'Fancy', ' ', 'Pants', '/', 'The', ' ', 'Other', ' ', 'one' ],
		];

		foreach ( $tests as $test => $expected ) {
			$this->assertEquals( $expected, $tokenizer->tokenizeString( $test ), $test . ' failed standard tokenization' );
		}
	}
}