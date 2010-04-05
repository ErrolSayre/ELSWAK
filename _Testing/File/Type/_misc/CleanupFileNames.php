<?php
// include the data type detector
require_once('ELSWAK/File/Type/Detector.php');

// read the file name from the post data
$dirtyName = '';
$cleanName = '';
if (isset($_POST['fileName']))
{
	$dirtyName = $_POST['fileName'];
	
	// clean up nasty characters in the name
	$cleanName = preg_replace('/[^a-zA-Z0-9\.]/i', '_', $dirtyName);
}

// output the form
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.LF;
echo '<input type="text" name="fileName" value="'.$dirtyName.'">'.LF;
echo '<input type="submit" value="Go">'.LF;
echo '</form>'.LF;

// output the previous name and results
echo '<p><b>Submitted Name:</b> '.$dirtyName.' ('.ELSWAK_File_Type_Detector::typeFromName($dirtyName).')</p>'.LF;
echo '<p><b>Cleaned Name:</b> '.$cleanName.' ('.ELSWAK_File_Type_Detector::typeFromName($cleanName).')</p>'.LF;
?>
