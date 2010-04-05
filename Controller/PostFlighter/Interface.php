<?php
/*
	ELSWAK Controller PostFlighter Interface
	
	This interface provides a common ancestor of controllers that need to perform actions after the response has been sent. (An example would be to save a log that a file download completed.)
	Please note that the "parent interface" (controller) and sister interfaces (pre-runner and post-runner) are all "pre-flight" interfaces, that require an activity be acted upon the response.
*/
interface ELSWAK_Controller_PostFlighter_Interface
{
	public function postFlight(array $arguments);
}
?>