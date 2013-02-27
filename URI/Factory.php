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



	public static function baseURLFromServerGlobal() {
		// create the URL for the current server from $_SERVER
		return self::baseURLFromServerGlobalLikeArray($_SERVER);
	}
	public static function baseURLFromServerGlobalLikeArray(array $data) {
		$url = new ELSWAK_HTTP_URL;
		$url->scheme = array_key_exists('HTTPS', $data)? 'https': 'http';
		
		// try to determine the hostname
		// take the first available value in order of the field list
		foreach (self::acceptableHostFields() as $field) {
			if (array_key_exists($field, $data)) {
//TODO Add checks here to sanitize host name as provided via HTTP_HOST and sometimes SERVER_NAME
				// ensure we don't use an empty value
				if ($data[$field]) {
					$url->host = $data[$field];
					break;
				}
			}
		}
		if (array_key_exists('SERVER_PORT', $data) && $data['SERVER_PORT'] != 80 && $data['SERVER_PORT'] != 443) {
			$url->port = $data['SERVER_PORT'];
		}
		return $url;
	}
	public static function applicationURLFromServerGlobal() {
		return self::applicationURLFromServerGlobalLikeArray($_SERVER);
	}
	public static function applicationURLFromServerGlobalLikeArray(array $data) {
		// take the base url and add the path to the current php script (assuming that the application uses "Pretty URLs" that omit the script name
		$url = self::baseURLFromServerGlobalLikeArray($data);
		if (array_key_exists('PHP_SELF', $data)) {
			$url->path = dirname($data['PHP_SELF']).'/';
		}
		return $url;
	}
	public static function urlFromServerGlobal() {
		return self::urlFromServerGlobalLikeArray($_SERVER);
	}
	public static function urlFromServerGlobalLikeArray(array $data) {
		// take the base url and add the requst uri
		$url = self::baseURLFromServerGlobalLikeArray($data);
		// import the request uri components (this should just override the things that are set)
		if (array_key_exists('REQUEST_URI', $data)) {
			$url->_import(parse_url($data['REQUEST_URI']));
		}
		return $url;
	}
	public static function acceptableHostFields() {
		return array(
			'SERVER_NAME',
			'HTTP_HOST',
		);
	}
}