<?php
/*
	Test the ELSWebAppKit File Response
*/
require_once('ELSWebAppKit/File/Response.php');
$response = new ELSWebAppKit_File_Response();
$response->setFile('test.txt');
$response->setFileName('SuperFile.txt');
$response->setDownload();
//$response->setInline();
$response->addContent('WHOO HOO');

if (true)
	$response->send();
else
	print_r_html($response);