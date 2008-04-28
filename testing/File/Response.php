<?php
/*
	Test the ELSWebAppKit File Response
*/
require_once('ELSWebAppKit/File/Response.php');
$response = new ELSWebAppKit_File_Response();
$response->addContent('test.txt');
$response->setDownload('theFile.txt');
//$response->setDownload(false);
//$response->setDownload();
$response->send();
?>