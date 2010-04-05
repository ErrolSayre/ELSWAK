<?php
require_once 'ELSWAK/Validator.php';
$valid = new ELSWAK_Validator();

echo '200 valid integer? ';
echo ($valid->integer(200)? 'yes': 'no').BR.LF;

echo '"200" valid integer? ';
echo ($valid->integer('200')? 'yes': 'no').BR.LF;

echo '200.1 valid integer? ';
echo ($valid->integer(200.1)? 'yes': 'no').BR.LF;

echo '"200.1" valid integer? ';
echo ($valid->integer('200.1')? 'yes': 'no').BR.LF;

echo '200+1 valid integer? ';
echo ($valid->integer(200+1)? 'yes': 'no').BR.LF;

echo '"200+1" valid integer? ';
echo ($valid->integer('200+1')? 'yes': 'no').BR.LF;

echo '"200"+"1" valid integer? ';
echo ($valid->integer('200'+'1')? 'yes': 'no').BR.LF;

echo '"23asdf" valid integer? ';
echo ($valid->integer('23asdf')? 'yes': 'no').BR.LF;
