<?php
/**
 * Provide a tool for parsing a string to build a URI safe component (i.e. value that can go between
 * /s in a URI).
 *
 * Some other projects have demonstrated this as a common requirement (e.g. taking an object's name
 * and inventing a URI from it) so I'm implementing this to provide a common structure for this.
 *
 * Essentially the process for doing this is thus:
 * 1. prepare the string for tokenization —remove any characters known to be problematic (e.g. 's)
 * 2. tokenize the string —use a series of characters that can serve as boundaries (e.g. whitespace,
 *    hypens, other punctuation)
 * 3. process tokens, removing or replacing known elements (e.g. and => +) or reformating the token
 * 4. combine tokens into component (merging with hyphen)
 * 5. determine replacement for the entire parsed component (e.g. apple-computer => apple)
 *
 * These steps combined with overridable methods for providing the removals/replacements give a
 * standardized but heavily customizable approach to other objects.
 */

class ELSWAK_URI_Componentizer {



	/**
	 * Invent a URI component out of the value.
	 *
	 * This method does no URI safety checks and assumes that this label will be coming from a
	 * sanitized source rather than directly from the user.
	 *
	 * @param mixed $value
	 * @param array $tokenBoundaries strings that can be replaced with a space to become a token value
	 * @param array $preTokenReplacements key/value pairs where keys should be replaced by value
	 * @param array $tokenReplacements key/value pairs where keys should be replaced by value
	 * @param array $componentReplacements key/value pairs where keys should be replaced by value
	 * @return string URI component
	 */
	public static function parseWithOptions( $value, array $tokenBoundaries, array $preTokenReplacements, array $tokenReplacements, array $componentReplacements ) {
		
		// first process the input string, replacing any values from the preTokenReplacements
		$value = str_replace( array_keys( $preTokenReplacements ), array_values( $preTokenReplacements ), $value );
		
		// normalize case and break into tokens
		$tokens = explode( ' ', str_replace( $tokenBoundaries, ' ', strtolower( $value ) ) );
		
		// setup the storage for final URI component sub-components
		$subComponents = array();
		foreach ( $tokens as $token ) {
			// determine if there is a specific replacement for this token
			if ( array_key_exists( $token, $tokenReplacements ) ) {
				$token = $tokenReplacements[ $token ];
			}
			
			// only include tokens that are non-empty
			if ( $token ) {
				$subComponents[] = urlencode( $token );
			}
		}
		
		// reassemble the sub-components
		$final = implode('-', $subComponents);
		
		// look for final replacements
		if ( array_key_exists( $final, $componentReplacements ) ) {
			return $componentReplacements[ $final ];
		}
		return $final;
	}

	/**
	 * Invent a URI component out of the value using a Componentizable class
	 *
	 * @param mixed $value
	 * @param ELSWAK_URI_Componentizable $source
	 * @return string URI component
	 */
	public static function parseWithOptionsFromObject( $value, ELSWAK_URI_Componentizable $source ) {
		return self::parseURIWithOptions( $value, $source->uriTokenBoundaries(), $source->uriPreTokenReplacements(), $source->uriTokenReplacements(), $source->uriComponentReplacements() );
	}

	/**
	 * Invent a URI component out of the value using default options
	 *
	 * @param mixed $value
	 * @return string URI component
	 */
	public static function parseURIComponent( $value ) {
		return self::parseWithOptions(
			$value,
			self::uriTokenBoundaries(),
			self::uriPreTokenReplacements(),
			self::uriTokenReplacements(),
			self::uriComponentReplacements()
		);
	}



	/**
	 * Return a list of strings that can be used to break the value into tokens
	 *
	 * @return array
	 */
	public static function uriTokenBoundaries() {
		return array(
			' ',
			'-',
			'/',
			'.',
			',',
		);
	}



	/**
	 * Return a list of strings to remove/replace before tokenizing
	 *
	 * Unlike the previous methods, I'm starting this one out with a purely replacement based
	 * approach allowing “removables” to be indicated by a null value. Generally, however, these
	 * strings will need to be replaced with a character that gurantees a token break so the value
	 * should be a space.
	 *
	 * @return array
	 */
	public static function uriPreTokenReplacements() {
		return array(
			',' => ' ',
			'&' => ' ',
			"'" => ' ',
			'.' => ' ',
			'/' => ' ',
		);
	}



	/**
	 * Return a list of token replacements
	 *
	 * To indicate the token should be totally removed, provide a null value for it's replacement.
	 *
	 * @return array
	 */
	public static function uriTokenReplacements() {
		return array(
			'-' => null,
		);
	}



	/**
	 * Provide a list of component replacements
	 *
	 * This method is setup to be overridden by subsclassed items.
	 *
	 * @return array
	 */
	public static function uriComponentReplacements() {
		return array(
		);
	}
}