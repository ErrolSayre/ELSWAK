<?php
require_once('ELSWebAppKit/HTML/Response.php');
$response = new ELSWebAppKit_HTML_Response();
$response->document()->appendChild($response->document()->createElement('h1', 'Hello World!'));
//$response->setRedirect('http://www.research.olemiss.edu/cms/');
$response->send();
?>