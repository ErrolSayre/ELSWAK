<?php
class ELSWAK_File
	extends ELSWAK_Object {

	protected $path;
	protected $name;
	protected $extension;
	protected $type;



	public function __construct($path) {
		$this->setPath($path);
	}



	public function setPath($path) {
		$this->path = $path;
		$this->name = pathinfo($path, PATHINFO_BASENAME);
		$this->extension = pathinfo($path, PATHINFO_EXTENSION);
		$this->type = ELSWAK_File_Type_Detector::typeFromExtension($this->extension);
		return $this;
	}
	protected function setName() {}
	protected function setExtension() {}
	protected function setType() {}



	public static function sanitizeFileName($name, $pretty = true) {
		$sanitized = '';
		
		// filter the standard replacements
		$replacements = array(
			'/\s/' => '_',
			'/\&/' => '_',
			'/\+/' => '_',
			'/__/' => '_',
			'/\//' => '-',
		);
		// filter the "pretty" replacements if requested
		if ($pretty) {
			$replacements['/\&/'] = '_and_';
			$replacements['/\+/'] = '_plus_';
		}
		$name = preg_replace(array_keys($replacements), $replacements, $name);
		
		// filter out everything but alphanumeric, underscore, and dot characters
		for ($i = 0; $i < strlen($name); ++$i) {
			$character = substr($name, $i, 1);
			if (ctype_alnum($character) == TRUE || $character == '_' || $character == '-' || $character == '.') {
				$sanitized .= $character;
			}
		}
		return $sanitized;
	}
}
