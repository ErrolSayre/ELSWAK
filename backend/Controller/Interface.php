<?php
/*
	ELSWebAppKit Controller Interface
	
	This interface provides a common ancestor to controllers, which must provide a run method to perform their actions upon the response before it is sent.
*/
interface ELSWebAppKit_Controller_Interface
{
	public function run(ELSWebAppKit_HTTP_Response $response, array $request);
}
?>