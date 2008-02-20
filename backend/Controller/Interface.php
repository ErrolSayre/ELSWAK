<?php
/*
	ELSWebAppKit Controller Interface
	
*/
interface ELSWebAppKit_Controller_Interface
{
	public function preRun(ELSWebAppKit_HTTP_Response $response, array $request);
	public function run(ELSWebAppKit_HTTP_Response $response, array $request);
	public function postRun(ELSWebAppKit_HTTP_Response $response, array $request);
}
?>