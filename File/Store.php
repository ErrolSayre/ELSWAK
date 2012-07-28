<?php
/*
	ELSWAK File Store
	
	This class provides a generic mechanism for storing data in a file. It supports file "formats" via data coders. The default data coder uses PHP serialize to make the data store as agnostic as possible.
*/

class ELSWAK_File_Store_Exception extends ELSWAK_Exception {}

class ELSWAK_File_Store 
	implements ELSWAK_Batch_Store_Interface {
	protected $filePath;
	protected $coder;
	
	public function __construct($filePath, ELSWAK_Data_Coder_Interface $coder = null) {
		$this->setFilePath($filePath);
		if ($coder instanceof ELSWAK_Data_Coder_Interface) {
			$this->coder = $coder;
		} else {
			// utilize the default coder
			$this->coder = new ELSWAK_Data_Serializer;
		}
	}
	protected function setFilePath($path) {
		$this->filePath = $path;
		return $this;
	}
	public function read() {
		// load the data from the file
		if (($contents = file_get_contents($this->filePath)) !== false) {
			return $this->coder->decode($contents);
		}
		throw new ELSWAK_File_Store_Exception('Unable to read data from file at path: '.$this->filePath);
	}
	public function write($data) {
		if (file_put_contents($this->filePath, $this->coder->encode($data)) !== false) {
			return true;
		}
		throw new ELSWAK_File_Store_Exception('Unable to write data to file at path: '.$this->filePath);
	}
}