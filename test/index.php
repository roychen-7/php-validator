<?php

require '../Validator.class.php';

$v = Validator::get_instance();

$a = 1;
$rules = array(
	'a'	=> array('int', true)
);
var_dump($v->validate($rules, array(
	'a'	=> $a
)));


?>
