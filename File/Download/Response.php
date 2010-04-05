<?php
/*
	ELSWAK File Download Response
	
	This class handles responses which collect data and send it to the user as a download.
*/
require_once('ELSWAK/HTTP/Response.php');
require_once('ELSWAK/File/Type/Detector.php');
class ELSWAK_File_Download_Response
	extends ELSWAK_HTTP_Response
{
	protected $fileName;
	
	public function __construct($fileName = null)
	{
		parent::__construct();
		
		$this->setHeader('Content-Transfer-Encoding', 'binary', true);
		
		if ($fileName !== null)
			$this->setFileName($fileName);
		
		$this->setDownload();
	}
	protected function setDownload()
	{
		if ($this->fileName != false)
			$this->setHeader('Content-Disposition', 'attachment; filename='.$this->fileName, true);
		else
			$this->setHeader('Content-Disposition', 'attachment', true);
		return $this;
	}
	public function setFileName($fileName = null)
	{
		if (is_string($fileName))
			$this->fileName = $fileName;
		else
			throw new Exception('Unable to set file name. Please provide a valid string.');
		
		$this->setHeader('Content-Type', ELSWAK_File_Type_Detector::typeFromName($this->fileName), true);
		
		return $this->setDownload();
	}
	public function addMessage($message)
	{
		return $this;
	}
}