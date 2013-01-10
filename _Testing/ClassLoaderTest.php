<?php
class ELSWAK_ClassLoaderTest
	extends PHPUnit_Framework_TestCase {
	
	public function testDefaultConstructor() {
		$count = count(spl_autoload_functions());
		$var = new ELSWAK_ClassLoader;
		$this->assertEquals($count + 1, count(spl_autoload_functions()));
		
		// look in the registered loaders for this object
		$loaders = spl_autoload_functions();
		foreach ($loaders as $loader) {
			if (is_array($loader)) {
				if ($loader[0] === $var) {
					$this->assertEquals($var, $loader[0]);
				}
			}
		}
		
		// unregister the loader
		$var->unregister();
		$this->assertEquals(count($loaders) - 1, count(spl_autoload_functions()));
	}
	
	public function testConstructorWithOptions() {
		// this option will create a class loader with a cache file located within the testing directory
		$path = dirname(__FILE__);
		$appPath = dirname(dirname($path));
		$cacheFile = $path.'/ClassLoaderTest.cache';
		$var = new ELSWAK_ClassLoader($cacheFile, $appPath, true, false);
		// load a class file with this instance specifically
		$var->loadClass('ELSWAK_ClassLoaderDummy');
		
		// destroy this variable and ensure the cache file is written
		unset($var);
		
		$this->assertEquals(true, is_readable($cacheFile));
		$cacheContents = file_get_contents($cacheFile);
		
		// now create another loader to use this cache file
		$var2 = new ELSWAK_ClassLoader($cacheFile, null, null, null);
		$this->assertStringEndsWith('ClassLoaderDummy.php', ' '.$var2->cachedFileForClass('ELSWAK_ClassLoaderDummy'));
		
		// attempt to locate the dummy class again (with different case) to get coverage on that path
		$var2->loadClass('ELSWAK_ClassLOaderdummy');
		// also skip to the locate file method to get coverate there
		$var2->locateClassFile('ELSWAK_ClassLoaderDummy');
		
		
		// write the cache file (again) and ensure they have the same contents
		$var2->storeCache();
		$this->assertEquals($cacheContents, file_get_contents($cacheFile));
		
		// throw away the cache file
		unlink($cacheFile);
	}
	
	public function testConstructorWithoutAutoRegister() {
		$count = count(spl_autoload_functions());
		$var = new ELSWAK_ClassLoader(null, null, null, null);
		// ensure the auto-loader didn't auto register
		$this->assertEquals($count, count(spl_autoload_functions()));
	}
	
	public function testConstructorWithArrayOfPaths() {
		$paths = array(
			dirname(__FILE__),
			dirname(dirname(__FILE__))
		);

		$var = new ELSWAK_ClassLoader(null, $paths, false, false);
		$this->assertEquals($paths, $var->classPaths());
	}
	
	public function testPaths() {
		$this->assertStringEndsWith('ELSWAK', ELSWAK_ClassLoader::path());
		$this->assertStringEndsWith('_Storage', ELSWAK_ClassLoader::storagePath());
	}
}
