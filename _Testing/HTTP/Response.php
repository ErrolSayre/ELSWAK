<?php
require_once '../setup-environment.php';

$response = new ELSWAK_HTTP_Response();
$response->addContent($response->serverUri().BR);
$response->addContent($response->applicationPath().BR);
$response->addMessage('The thingamajig didn’t work like a who’s-a-what’s-it');
$response->addMessage('User not authenticated');
$response->addMessage('User authentication form displayed.');
$response->setStatus('Looking Good');
$response->setHeader('Gillibush', 'Ferriwinkle');
$response->setHeader('Jonny-Cab', 'To the Moon');
$response->setExpires(time() + 5);
$response->addContent('New and different: '.date('m/d/Y H:i:s').BR);
$response->setContentType();
$response->addContent($response->messages(BR.LF));

if (isset($_REQUEST['full']))
	$response->send();
else if (isset($_REQUEST['content']))
	$response->sendContent();
else
	var_dump($response);