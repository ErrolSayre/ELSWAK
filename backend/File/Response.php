<?php
/*
	ELSWebAppKit File Response
	
	This class handles responses which read files on the local filesystem to the response.
*/
require_once('ELSWebAppKit/HTTP/Response.php');
require_once('ELSWebAppKit/File/Type/Detector.php');
class ELSWebAppKit_File_Response
	extends ELSWebAppKit_HTTP_Response
{
	public function __construct($filePath = null, $fileName = null)
	{
		parent::__construct();
		$this->setHeader('Content-Transfer-Encoding', 'binary', true);
		if ($filePath !== null)
			$this->addContent($filePath);
		if ($fileName !== null)
			$this->setDownload($fileName);
	}
	public function setDownload($fileName = null)
	{
		if ($fileName === false)
			$this->setHeader('Content-Disposition', 'inline', true);
		else if (is_string($fileName))
			$this->setHeader('Content-Disposition', 'attachment; filename='.$fileName, true);
		else
			$this->setHeader('Content-Disposition', 'attachment', true);
	}
	public function addContent($content)
	{
		$this->body = $content;
		
		// determine the file type and set a header for it
		$this->setHeader('Content-Type', ELSWebAppKit_File_Type_Detector::typeFromName($this->body), true);
		
		return $this;
	}
	public function addMessage($message)
	{
		return $this;
	}
	public function sendBody()
	{
		if (is_readable($this->body))
		{
			$this->setHeader('Content-Length', filesize($this->body), true);
			readfile($this->body);
		}
	}
}
?>