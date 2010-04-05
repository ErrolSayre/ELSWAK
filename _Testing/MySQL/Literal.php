<?php
require_once('ELSWAK/MySQL/Literal.php');

$literal = new ELSWAK_MySQL_Literal(1);
print_r_html($literal);
echo $literal->sql().BR;
$literal->setLiteral('asdf');
echo $literal->sql().BR;
$literal->setLiteral('10.0789e+12');
echo $literal->sql().BR;
$literal->setLiteral('1.000.0028');
echo $literal->sql().BR;
$literal->setLiteral('VIBARNY');
echo $literal->sql().BR;
$literal->setLiteral('as df');
echo $literal->sql().BR;
?>