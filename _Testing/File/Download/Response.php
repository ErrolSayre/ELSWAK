<?php
require 'ELSWAK/File/Download/Response.php';
$response = new ELSWAK_File_Download_Response();
$response->setFileName('Test.csv');
$response->addContent('"Investigator ID","First Name"');
$response->addContent('"23","John"');
$response->addContent('"45","Mark"');
if (true)
	$response->send();
else if (true)
	$response->sendContent();
else
	print_r_html($response);