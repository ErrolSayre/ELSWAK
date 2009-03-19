<?php
require_once 'ELSWebAppKit/Validator.php';
$valid = new ELSWebAppKit_Validator();

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
