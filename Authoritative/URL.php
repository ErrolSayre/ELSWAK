<?php
/**
 * Respresent a URL as components.
 *
 * This class provides a generic "authoritative" URL representation. I'm classifying URLs that contain an "authority" section as authoritative URLs. The primary focus is representing HTTP(S) URLs so it includes attributes used by them but possibly ignored by other schemes. Essentially any URL where the scheme is followed by '//' and then an authority string can be represented. Also supported are server relative HTTP URLs (i.e. those that omit the scheme and authority but contain a path) which totally omit the authority.
 * @author Errol Sayre
 * @package ELSWAK
 */
class ELSWAK_Authoritative_URL
	extends ELSWAK_URL {
	
	protected $user;
	protected $password;
	protected $host;
	protected $port;
	protected $pathComponents;
	protected $target;
	protected $type;
	
	/**
	 * Override hierarchy method
	 * @return string
	 */
	public function hierarchy() {
		$hierarchy = '';
		if ($this->hasAuthority()) {
			$hierarchy = '//'.$this->authority();
		}
		if ($this->hasPath()) {
			$hierarchy .= $this->path();
		}
		return $hierarchy;
	}
	/**
	 * Build the authority section of the URL
	 * @return string
	 */
	public function authority() {
		// assemble the authority from the various components
		
		// omit the authority if there is no host
		$authority = '';
		if ($this->host) {
			// add the user and password if available
			if ($this->user) {
				$authority .= urlencode($this->user);
				if ($this->password) {
					$authority .= ':'.urlencode($this->password);
				}
				$authority .= '@';
			}
			
			// add the host
			$authority .= $this->host;
			
			// add the port if available
			if ($this->port) {
				$authority .= ':'.$this->port;
			}
		}
		
		// return the finished string
		return $authority;
	}
	public function hasAuthority() {
		if ($this->host) {
			return true;
		}
		return false;
	}
	
	public function setUser($user) {
		$this->user = urldecode($user);
		return $this;
	}
	
	public function setPassword($password) {
		$this->password = urldecode($password);
		return $this;
	}
	
	public function setPort($port) {
		$this->port = intval($port);
		return $this;
	}
	
	
	/**
	 * Override the set path method to break the string into components.
	 * @return ELSWAK_URN self
	 */
	public function setPath($path = null) {
		$path = ltrim($path, '/');
		// after trimming, determine if there is anything left
		if ($path) {
			$parts = explode('/', $path);
			$file = array_pop($parts);
			if ($file) {
				$this->target = pathinfo($file, PATHINFO_FILENAME);
				$this->type = pathinfo($file, PATHINFO_EXTENSION);
			}
			$this->setPathComponents($parts);
		} else {
			// since there was simply a trailing slash, force outputting one by setting the target to false
			$this->target = false;
		}
		return $this;
	}
	public function path() {
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
		} elseif (!count($items)) {
			// since there are not items and no target, only include a leading slash if the target is specifically false or if there is a query or fragment
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
	public function hasPath() {
		return true;
	}
	public function setPathComponents(array $pathComponents = null) {
		if (is_array($pathComponents)) {
			$this->pathComponents = array_values($pathComponents);
		} else {
			$this->pathComponents = array();
		}
		return $this;
	}
	public function pathComponents() {
		// utilize lazy initialization
		if (!is_array($this->pathComponents)) {
			$this->pathComponents = array();
		}
		return $this->pathComponents;
	}
	
	public function uri() {
		// reassemble the various components as a complete URI
		$uri = '';
		if ($this->scheme) {
			$uri = $this->scheme().':';
		}
		$uri .= $this->hierarchy();
		if ($this->hasQuery()) {
			$uri .= '?'.$this->query();
		}
		if ($this->hasFragment()) {
			$uri .= '#'.$this->fragment();
		}
		return $uri;
	}



	/**
	 * Construct a server URI
	 *
	 * Construct the URI for the server, including all aspects of the
	 * authority section.
	 *
	 * @return string
	 */
	public function serverURI() {
		$uri = '';
		if ($this->scheme) {
			$uri = $this->scheme().':';
		}
		if ($this->hasAuthority()) {
			$uri .= '//'.$this->authority();
		}
		return $uri;
	}
}