<?php
require_once('ELSWebAppKit/HTML/Response.php');
$response = new ELSWebAppKit_HTML_Response();
$response->addContent($response->createElement('h1', 'Hello World!'));
$response->addContent('<p>what’s happenin?</p>', 'TheParagraph', 'html');
$response->addContent('<p>insert inside joke here</p>', 'TheParagraph', 'html');
//$response->setRedirect('http://www.research.olemiss.edu');
$response->send();
?>