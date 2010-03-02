<?php
/*
	ELSWebAppKit Controller PostRunner Interface
	
	This interface provides a common ancestor for controllers that need to perform post-run, pre-flight activities on the response object.
*/
interface ELSWebAppKit_Controller_PostRunner_Interface {
	public function postRun(ELSWebAppKit_HTTP_Response $response, array $arguments);
}