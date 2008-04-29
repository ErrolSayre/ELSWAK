<?php
/*
	ELSWebAppKit Controller PreRunner Interface
	
	This interface provides a common ancestor for controllers which need to perform actions on the response before the responsible controller runs.
*/
interface ELSWebAppKit_Controller_PreRunner_Interface
{
	public function preRun(ELSWebAppKit_HTTP_Response $response, array $request);
}
?>