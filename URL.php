<?php
/**
 * Respresent a URL as components.
 */
class ELSWAK_URL
	extends ELSWAK_Object {
	
	protected $scheme;
	protected $user;
	protected $password;
	protected $host;
	protected $port;
	protected $pathComponents;
	protected $target;
	protected $type;
	protected $queryComponents;
	protected $fragment;
	
	public function authority() {
		// assemble the authority from the various components
		// start with the scheme
		$authority = $this->scheme;
		
		// add the appropriate delimiter
		// (this will be blank if there is no scheme)
		$authority .= $this->delimiterForScheme($this->scheme);
		
		// add the user and password if available
		if ($this->user) {
			$authority .= $this->user;
			if ($this->password) {
				$authority .= ':'.$this->password;
			}
			$authority .= '@';
		}
		
		// add the host
		$authority .= $this->host;
		
		// add the port if available
		if ($this->port) {
			$authority .= ':'.$this->port;
		}
		
		// return the finished string
		return $authority;
	}
	
	public function pathComponents() {
		// utilize lazy initialization
		if (!is_array($this->pathComponents)) {
			$this->pathComponents = array();
		}
		return $this->pathComponents;
	}
	public function path() {
		// look for special case forms
		if ($this->delimiterForScheme($this->scheme) == ':') {
			if ($this->type) {
				return $this->target.'.'.$this->type;
			}
			return $this->target;
		}
		
		// for items that have a path like hierarchy, reassemble this hierarchy
		$items = $this->pathComponents();
		$leadingSlash = '/';
		$trailingSlash = '/';
		if ($this->target) {
			// since there is a target, there should be no trailing slash
			$trailingSlash = '';
			if ($this->type) {
				$items[] = $this->target.'.'.$this->type;
			} else {
				$items[] = $this->target;
			}
		} elseif (count($items)) {
			// since there is no target, but there are path components, there must be a leading and trailing slash
		} else {
			// since there is no target, only include a leading slash if the target is specifically false or if there is a query or fragment
			$trailingSlash = '';
			// determine if there is a query or fragment
			if ($this->target === false || $this->hasQuery() || $this->hasFragment()) {
				// since the target is specifically null or there is a query or fragment, ensure a slash after the domain
			} else {
				// since all of the above is false, offer no slash after the domain
				$leadingSlash = '';
			}
		}
		return $leadingSlash.implode('/', $items).$trailingSlash;
	}
	
	public function setQueryComponents(ELSWAK_Dictionary $list) {
		$this->queryComponents = $list;
		return $this;
	}
	public function queryComponents() {
		// utilize lazy instantiation
		if (!($this->queryComponents instanceof ELSWAK_Dictionary)) {
			$this->queryComponents = new ELSWAK_Dictionary;
		}
		return $this->queryComponents;
	}
	public function query() {
		// reassemble the components into a query string
		return http_build_query($this->queryComponents()->store);
	}
	public function hasQuery() {
		return $this->queryComponents()->hasItems();
	}
		
	public function hasFragment() {
		if ($this->fragment) {
			return true;
		}
		return false;
	}
	
	public function url() {
		// reassemble the various components as a complete URL
		// first reassemble the authority
		$url = $this->authority();
		
		// add the path
		$url .= $this->path();
		
		// add the query
		if ($this->hasQuery()) {
			$url .= '?'.$this->query();
		}
		
		// add the fragment
		if ($this->hasFragment()) {
			$url .= '#'.$this->fragment;
		}
		
		// return the finished url
		return $url;
	}
	public function __toString() {
		return $this->url();
	}
	
	public static function urlForString($string) {
		// break up the string into it's components
		$components = parse_url($string);
		
		// translate the password to the correct property name
		if (array_key_exists('pass', $components)) {
			$components['password'] = $components['pass'];
		}
		
		// break up the path into components
		if (array_key_exists('path', $components)) {
			$path = ltrim($components['path'], '/');
			// after trimming, determine if there is anything left
			if ($path) {
				$parts = explode('/', $path);
				$file = array_pop($parts);
				if ($file) {
					$components['target'] = pathinfo($file, PATHINFO_FILENAME);
					$components['type'] = pathinfo($file, PATHINFO_EXTENSION);
				}
				$components['pathComponents'] = $parts;
			} else {
				// since there was simply a trailing space, force a trailing slash by setting the target to false
				$components['target'] = false;
			}
		}
		
		// break up the query into key/value pairs
		if (array_key_exists('query', $components)) {
			$query = new ELSWAK_Dictionary;
			$pairs = explode('&', $components['query']);
			foreach ($pairs as $pair) {
				list($key, $value) = explode('=', $pair.'=');
				$query->set(urldecode($key), urldecode($value));
			}
			$components['queryComponents'] = $query;
		}
		
		return new ELSWAK_URL($components);
	}
	
	public static function delimiterForScheme($scheme) {
		$colon = array(
			'mailto',
			'urn',
		);
		if (in_array($scheme, $colon)) {
			return ':';
		} elseif ($scheme) {
			return '://';
		}
		return '';
	}
}