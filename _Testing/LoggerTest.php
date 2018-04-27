<?php
class ELSWAK_LoggerTest
	extends PHPUnit\Framework\TestCase {

	
	public function testLogger() {
		// create a logger that should immediate send errors and store messages for retrieval
		$errorCount = 0;
		$log = dirname(__FILE__).'/LoggerTest.log';
		// clear out any existing log data
		if (is_file($log)) {
			unlink($log);
		}
		//error_log('Temporarily redirecting errors to '.$log);
		ini_set('error_log', $log);
		$logger = new ELSWAK_Logger(true, false, true, false);
		$logger->message('A message.');
		
		// assert that there is one message
		$this->assertEquals(1, $logger->totalMessages);
		
		// add an error (which should go straight to the log)
		$logger->error('An error.');
		++$errorCount;
		// assert that the message count didn't increase
		$this->assertEquals(1, $logger->totalMessages);
		
		// count the number of lines in the log file and assert they're the same as our expected value
		$this->assertEquals($errorCount, count(file($log)));
		
		// now set the logger to not flush
		$logger->setFlushImmediately(false);
		
		// add another error and message
		$logger->error('Another error');
		++$errorCount;
		$logger->message('And another message');
		
		// assert that the message count is now 3
		$this->assertEquals(3, $logger->totalMessages);
		
		// count the number of lines in the log file and assert they're the less than our error count
		$this->assertLessThan($errorCount, count(file($log)));
		
		// now flush the errors
		$logger->flush();
		
		// assert that the message count is now 2
		$this->assertEquals(2, $logger->totalMessages);
		
		// count the number of lines in the log file and assert they're the same as our expected value
		$this->assertEquals($errorCount, count(file($log)));
		
		// now set the logger to "display" messages
		$logger->setDisplayMessages();
		
		// now flush
		$messageCount = $logger->totalMessages;
		$logger->flush();
		
		// now count the lines and assert they're errors+messages
		$this->assertEquals($errorCount + $messageCount, count(file($log)));
		
		// restore the error log as before
		ini_restore('error_log');
		//error_log('Restored error_log destination.');
	}
}