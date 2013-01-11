<?php
/**
 * Produce various types of URIs from one parser.
 * @author Errol Sayre
 * @package ELSWAK
 */
class ELSWAK_URI_Factory {
	public static function uriForString($string) {
		// break up the string into it's components
		$components = parse_url($string);
		
		// determine the scheme, defaulting to HTTP
		$scheme = 'http';
		if (array_key_exists('scheme', $components)) {
			$scheme = strtolower($components['scheme']);
		}
		
		// branch on the type of URI
		if ($scheme == 'mailto') {
			return self::emailURLForComponents($components);
		} elseif ($scheme == 'urn') {
			return self::urnForComponents($components);
		}
		// treat the URI as HTTP like (since most are)
		return self::urlForComponents($components);
	}
	public static function emailURLForComponents($components) {
		return new ELSWAK_Email_URL($components);
	}
	public static function urnForComponents($components) {
		return new ELSWAK_URN($components);
	}
	public static function urlForComponents(array $components) {
		// translate the password to the correct property name
		if (array_key_exists('pass', $components)) {
			$components['password'] = $components['pass'];
		}
		
		// determine the final object type
		$scheme = '';
		if (array_key_exists('scheme', $components)) {
			$scheme = strtolower($components['scheme']);
		}
		
		// look for HTTP urls
		if ($scheme == 'http' || $scheme == 'https') {
			return new ELSWAK_HTTP_URL($components);
		}
		
		// return the generic URL of the form scheme://
		return new ELSWAK_Authoritative_URL($components);
	}
}