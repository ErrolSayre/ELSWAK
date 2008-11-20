<?php
require 'ELSWebAppKit/File/Download/Response.php';
$response = new ELSWebAppKit_File_Download_Response();
$response->setFileName('Test.csv');
$response->addContent('"Investigator ID","First Name"');
$response->addContent('"23","John"');
$response->addContent('"45","Mark"');
if (false)
	$response->send();
else if (true)
	$response->sendBody();
else
	print_r_html($response);