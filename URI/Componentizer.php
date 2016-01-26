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
	 * @param array $finalReplacements key/value pairs where keys should be replaced by value
	 * @param string $formatting indicator for formatting to be applied to tokens before implosion
	 * @return string URI component
	 */
	public static function parseWithOptions( $value, array $tokenBoundaries, array $tokenReplacements, array $finalReplacements, $formatting = 'url lowercase', $caseSensitiveReplacements = false ) {

		// first tokenize the string
		$tokenizer = new ELSWAK_Tokenizer( $tokenBoundaries );
		$tokens = $tokenizer->tokenizeString( $value );

		// setup the storage for final URI component sub-components
		$subComponents = array();
		foreach ( $tokens as $token ) {

			// determine if there is a specific replacement for this token
			if ( array_key_exists( $token, $tokenReplacements ) ) {
				$token = $tokenReplacements[ $token ];
			}
			elseif ( !$caseSensitiveReplacements ) {

				// parse through the keys, looking for one that matches case-insensitively
				foreach ( $tokenReplacements as $key => $replacement ) {
					if ( strcasecmp( $token, $key ) == 0 ) {
						$token = $replacement;
					}
				}
			}

			// only include tokens that are non-empty
			if ( $token ) {

				// switch on the appropriate formatting
				if ( strpos( $formatting, 'upper' ) !== false ) {
					$token = strtoupper( $token );
				}
				if ( strpos( $formatting, 'lower' ) !== false ) {
					$token = strtolower( $token );
				}
				if ( strpos( $formatting, 'capitalize' ) !== false ) {
					$token = ucfirst( $token );
				}
				if ( strpos( $formatting, 'url' ) !== false ) {
					$token = urlencode( $token );
				}
				$subComponents[] = $token;
			}
		}

		// reassemble the sub-components
		$final = implode( '', $subComponents );

		// remove any leading or trailing boundary tokens when dealing with URL components
		if ( strpos( $formatting, 'url' ) !== false ||  strpos( $formatting, 'trim' ) !== false ) {
			$final = trim( $final, implode( '', $tokenBoundaries ) );
		}

		// trim any further whitespace
		$final = trim( $final );

		// issue any final replacements
		if ( $caseSensitiveReplacements ) {
			$final = str_replace( array_keys( $finalReplacements ), array_values( $finalReplacements ), $final );
		}
		else {
			$final = str_ireplace( array_keys( $finalReplacements ), array_values( $finalReplacements ), $final );
		}

		return $final;
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
			self::uriTokenReplacements(),
			self::uriFinalReplacements()
		);
	}

	/**
	 * Invent a label for this component (e.g. breadcrumb trail) out of the value
	 *
	 * Akin to the component, there are times when a shortened form of a value would be useful for
	 * labeling links (such as navigation items). Following the same sort of rules, remove various
	 * components and normalize capitalization.
	 *
	 * @param mixed $value
	 * @param array $tokenBoundaries strings that can be replaced with a space to become a token value
	 * @param array $preTokenReplacements key/value pairs where keys should be replaced by value
	 * @param array $tokenReplacements key/value pairs where keys should be replaced by value
	 * @param array $finalReplacements key/value pairs where keys should be replaced by value
	 * @param integer $formatting bitwise indicator for formatting to be applied to tokens before implosion
	 * @return string URI label
	 */
	public static function parseURILabelWithOptions( $value, array $tokenBoundaries, array $tokenReplacements, array $finalReplacements, $formatting = null, $caseSensitiveReplacements = false ) {

		return self::parseWithOptions( $value, $tokenBoundaries, $tokenReplacements, $finalReplacements, $formatting, $caseSensitiveReplacements );
	}

	/**
	 * Alias the label parsing method
	 *
	 * @param mixed value
	 * @return string URI label
	 */
	public static function parseURILabel( $value ) {

		// utilize the defaults from the aliased method
		return self::parseURILabelWithOptions(
			$value,
			self::labelTokenBoundaries(),
			self::labelTokenReplacements(),
			self::labelFinalReplacements()
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
			'&',
			"'",
		);
	}

	/**
	 * Provide a list of strings that can be used to break the value into tokens
	 *
	 * This method is setup to be overridden by subsclassed items.
	 *
	 * @return array
	 */
	public static function labelTokenBoundaries() {
		return ELSWAK_Tokenizer::standardTokenBoundaries();
	}



	/**
	 * Return a list of strings to remove/replace as tokens
	 *
	 * @return array
	 */
	public static function uriTokenReplacements() {
		return array(
			' ' => '-',
			',' => '',
			'&' => '-',
			"'" => '',
			'.' => '-',
			'/' => '-',
		);
	}



	/**
	 * Return a list of strings to remove/replace as tokens
	 *
	 * @return array
	 */
	public static function labelTokenReplacements() {
		return array(
		);
	}



	/**
	 * Return a list of string replacements to perform on the reassembled string
	 *
	 * @return array
	 */
	public static function uriFinalReplacements() {
		return array(
			'---' => '-',
			'--' => '-',
		);
	}



	/**
	 * Return a list of string replacements to perform on the reassembled string
	 *
	 * @return array
	 */
	public static function labelFinalReplacements() {
		return array();
	}
}