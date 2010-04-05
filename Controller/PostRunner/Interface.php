<?php
/*
	ELSWAK Controller PostRunner Interface
	
	This interface provides a common ancestor for controllers that need to perform post-run, pre-flight activities on the response object.
*/
interface ELSWAK_Controller_PostRunner_Interface {
	public function postRun(ELSWAK_HTTP_Response $response, array $arguments);
}