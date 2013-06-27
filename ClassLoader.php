<?php
/*
	ELSWAK Class Loader
	
	This class provides a file-system cached class autoloader.
	
	This class is inspired by Zend Framework's loader, and Anthony Bush's autoloader. I needed to write one that works with either due to the fact that my code follows the Zend Framework naming convention.
*/
require_once 'StandardConstants.php';

class ELSWAK_ClassLoader {
	protected $classPaths = array();
	protected $cacheFilePath = '';
	protected $classFileIndex = array();
	protected $newFiles = false;
	
	public function __construct($cachePath = null, $classPaths = null, $includeIncludePaths = false, $autoRegister = true) {
		if (is_array($classPaths)) {
			foreach ($classPaths as $path) {
				$this->addClassPath($path);
			}
		} else if (is_string($classPaths)) {
			$this->addClassPath($classPaths);
		}
		if ($includeIncludePaths) {
			// get the include path from the system
			$paths = explode(':', ini_get('include_path'));
			if (is_array($paths)) {
				foreach ($paths as $path) {
					$this->addClassPath($path);
				}
			}
		}
		
		// determine if a cache path was provided (or the default requested)
		if ($cachePath === true) {
			// utilize the default cache path
			$this->cacheFilePath = $this->path().'/ClassLoader.cache';
			$this->loadCache();
		} elseif (strlen($cachePath)) {
			// determine if the file exists
			if (is_writeable($cachePath)) {
				$this->cacheFilePath = $cachePath;
				$this->loadCache();
			} elseif (file_put_contents($cachePath, '') !== false) {
				$this->cacheFilePath = $cachePath;
			}
		}
		
		// register this instance as an auto loader
		if ($autoRegister) {
			$this->register();
		}
	}
	public function __destruct() {
		if ($this->newFiles) {
			$this->storeCache();
		}
		$this->unregister();
	}
	public function register() {
		spl_autoload_register(array($this, 'loadClass'));
	}
	public function unregister() {
		spl_autoload_unregister(array($this, 'loadClass'));
	}
	
	
	
	public function storeCache() {
		// attempt to write the cache data to the store
		if ($this->cacheFilePath) {
			file_put_contents($this->cacheFilePath, serialize($this->classFileIndex));
		}
	}
	public function loadCache() {
		if (is_file($this->cacheFilePath)) {
			$cache = unserialize(file_get_contents($this->cacheFilePath));
			// determine if there were new files before loading the cache
			$hasNewFiles = $this->newFiles;
			if (is_array($cache)) {
				foreach ($cache as $class => $file) {
					$this->cacheFileForClass($file, $class);
				}
			}
			// if there were no new files before loading cache denote this so we don't write a new cache file
			$this->newFiles = $hasNewFiles;
		}
	}



	/**
	 * Add a path to search for classes
	 *
	 * Ensure paths are unique to prevent duplicate calls to the
	 * file-system.
	 *
	 * @param string $path
	 * @return ELSWAK_ClassLoader self
	 */
	public function addClassPath($path) {
		// trim any trailing slash
		$path = rtrim($path, '/');
		if (is_dir($path)) {
			if (!in_array($path, $this->classPaths)) {
				$this->classPaths[] = $path;
			}
		}
		return $this;
	}
	public function classPaths() {
		return $this->classPaths;
	}
	public function loadClass($class) {
		// ensure we don't attempt to load the same class again (if called manually)
		if (!class_exists($class, false)) {
			$file = self::locateClassFile($class);
			if ($file && file_exists($file)) {
				include $file;
				return true;
			}
		}
		return false;
	}
	public function locateClassFile($class) {
		// check the cache
		$file = self::cachedFileForClass($class);
		if ($file) {
			return $file;
		} else {
			// search for the file in the various class paths
			$classFileName = $class;
			// search by Zend Framework naming convention if applicable
			if (strpos($class, '_') !== false) {
				$classFileName = str_replace('_', '/', $class);
			} elseif (strpos($class, '\\') !== false) {
				$classFileName = str_replace('\\', '/', $class);
			}
			foreach ($this->classPaths as $path) {
				$file = $path.'/'.$classFileName.'.php';
				if (is_file($file)) {
					// cache a copy of this file
					$this->cacheFileForClass($file, $class);
					return $file;
				}
			}
		}
		return false;
	}
	public function cacheFileForClass($file, $class) {
		// store this located file in the cache
		$this->classFileIndex[$class] = $file;
		// also cache this file under the class name lowercased
		$this->classFileIndex[strtolower($class)] = $file;
		// update the flag to denote there are new lookups to cache
		$this->newFiles = true;
		return $this;
	}
	public function cachedFileForClass($class) {
		if (!array_key_exists($class, $this->classFileIndex)) {
			// search the class file index case insensitively
			$classLower = strtolower($class);
			if (array_key_exists($classLower, $this->classFileIndex)) {
				// cache this capitalization of the classname
				$this->classFileIndex[$class] = $this->classFileIndex[$classLower];
			} else {
				return false;
			}
		}
		return $this->classFileIndex[$class];
	}
	public static function path() {
		return pathinfo(__FILE__, PATHINFO_DIRNAME);
	}
	public static function storagePath() {
		$path = self::path();
		if (is_dir($path.'/_Storage') || mkdir($path.'/_Storage')) {
			return $path.'/_Storage';
		}
		return false;
	}
}