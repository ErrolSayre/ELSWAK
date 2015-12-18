<?php
/**
 * ELSWAK Tokenizer
 *
 * This class provides a simple, and reusable means for tokenizing strings using an array of token
 * boundaries. Unlike using explode() or a split method, this class will return the token boundaries
 * as tokens themselves, allowing items to be reassembled exactly as found.
 *
 * For now, this class is written QnD in order to provide a simple interface for the needs of
 * another class. At a later point I want to optimize this (perhaps using regular expressions).
 */
class ELSWAK_Tokenizer {



	protected $tokenBoundaries;
	protected $encoding;




	public function __construct( array $tokenBoundaries, $encoding = null ) {
		$this->tokenBoundaries = $tokenBoundaries;

		if ( $encoding ) {
			$this->encoding = $encoding;
		}
		else {
			$this->encoding = mb_internal_encoding();
		}
	}



	public function tokenizeString( $value ) {

		$tokens = array();

		// process the input string one (multi-byte) character at a time
		$token = '';
		$position = 0;
		$length = mb_strlen( $value, $this->encoding );

		while ( $position < $length ) {

			// grab the current character
			$character = mb_substr( $value, $position, 1, $this->encoding );

			// determine if this character is a token boundary
			if ( in_array( $character, $this->tokenBoundaries ) ) {

				// append any prior token to the list
				if ( $token ) {
					$tokens[] = $token;
					$token = '';
				}

				// append this boundary item as its own token
				$tokens[] = $character;
			}
			else {
				// append this character to the current token
				$token .= $character;
			}

			++$position;
		}

		// add any trailing token
		if ( $token ) {
			$tokens[] = $token;
		}

		return $tokens;
	}



	public static function standardTokenBoundaries() {
		return array(
			' ',
			"\n",
			"\r",
			'.',
			'!',
			'?',
			'¡',
			'¿',
			'-',
			'/',
			"\\",
			':',
		);
	}



	public static function standardTokenizer() {
		return new ELSWAK_Tokenizer( self::standardTokenBoundaries() );
	}
}