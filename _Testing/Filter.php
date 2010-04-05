<?php
require_once 'ELSWAK/Filter.php';
$filter = new ELSWAK_Filter();

echo '200 filtered as integer ';
echo $filter->integer(200).BR.LF;

echo '"200" filtered as integer? ';
echo $filter->integer('200').BR.LF;

echo '200.1 filtered as integer? ';
echo $filter->integer(200.1).BR.LF;

echo '"200.1" filtered as integer? ';
echo $filter->integer('200.1').BR.LF;

echo '200+1 filtered as integer? ';
echo $filter->integer(200+1).BR.LF;

echo '"200+1" filtered as integer? ';
echo $filter->integer('200+1').BR.LF;

echo '"200"+"1" filtered as integer? ';
echo $filter->integer('200'+'1').BR.LF;

echo '"23asdf" filtered as integer? ';
echo $filter->integer('23asdf').BR.LF;
