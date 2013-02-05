<?php
/**
 * Provide an interface for a generic logger.
 *
 * The default implementation would simply wrap PHP's error_log function.
 * A more advanced implementation may save its messages to a database or
 * send as an attachment to an email.
 *
 * The generic assumption is that all loggers will at least support
 * errors and generic messages but further subclasses could be more
 * liberal with message types.
 *
 * @package ELSWAK\Log
 */
interface ELSWAK_Logger_Interface {
	public function error($string);
	public function message($string);
	public function flush();
}