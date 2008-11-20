<?php
/*
	ELSWebAppKit File Download Response
	
	This class handles responses which collect data and send it to the user as a download.
*/
require_once('ELSWebAppKit/HTTP/Response.php');
require_once('ELSWebAppKit/File/Type/Detector.php');
class ELSWebAppKit_File_Download_Response
	extends ELSWebAppKit_HTTP_Response
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
		
		$this->setHeader('Content-Type', ELSWebAppKit_File_Type_Detector::typeFromName($this->fileName), true);
		
		return $this->setDownload();
	}
	public function addMessage($message)
	{
		return $this;
	}
}