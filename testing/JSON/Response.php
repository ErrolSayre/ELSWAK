<?php
require_once('ELSWebAppKit/JSON/Response.php');
$response = new ELSWebAppKit_JSON_Response();
$response->addContent('item 1');
$response->addContent('item 2');
$response->addContent('first', 'firstName');
$response->addContent('last', 'lastName');
$response->addContent('{"firstName":"person","lastName":"1"}', 'json string 1');
$response->addContent('{"firstName":"person","lastName":"2"}', null, 'json');
$response->send();
?>