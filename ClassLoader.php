<?php
/*
	ELSWAK Class Loader
	
	This class provides a file-system cached class autoloader.
	
	This class is inspired by Zend Framework's loader, and Anthony Bush's autoloader. I needed to write one that works with either due to the fact that my code follows the Zend Framework naming convention.
*/
class ELSWAK_ClassLoader {
	protected $classPaths = array();
	protected $cacheFilePath = '';
	protected $classFileIndex = array();
	protected $newFiles = false;
	
	public function __construct($cachePath = null, $classPaths = null, $includeIncludePaths = false) {
		if (is_array($classPaths)) {
			foreach ($classPaths as $path) {
				$this->addClassPath($path);
			}
		} else if (is_string($classPaths)) {
			$this->addClassPath($classPaths);
		}
		if ($includeIncludePaths) {
// implement this later
		}
		if ($cachePath) {
			$this->cacheFilePath = $cachePath;
			$this->loadCache();
		}
		
		// register this instance as an auto loader
		spl_autoload_register(array($this, 'loadClass'));
	}
	public function __destruct() {
		if ($this->newFiles) {
			$this->storeCache();
		}
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
			foreach ($cache as $class => $file) {
				$this->cacheFileForClass($file, $class);
			}
			// if there were no new files before loading cache denote this so we don't write a new cache file
			$this->newFiles = $hasNewFiles;
		}
	}
	public function addClassPath($path) {
		if (is_dir($path)) {
			$this->classPaths[] = rtrim($path, '/');
		}
		return $this;
	}
	public function loadClass($class) {
		$file = self::locateClassFile($class);
		if ($file && file_exists($file)) {
			include $file;
			return true;
		}
		return false;
	}
	public function locateClassFile($class) {
		// first check the cache
		$file = self::cachedFileForClass($class);
		if ($file) {
			return $file;
		} else {
			// search for the file in the various class paths
			$classFileName = $class;
			// search by Zend Framework naming convention if applicable
			if (strpos($class, '_') !== FALSE) {
				$classFileName = str_replace('_', '/', $class);
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
}