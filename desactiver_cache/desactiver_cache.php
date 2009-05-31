<?php
#eviter le recalcul de jquery.js a chaque hit ...
$exceptions = array('jquery.js','forms_styles.css');
//var_dump($_SERVER['REQUEST_METHOD']);
$fond = isset($GLOBALS['fond'])?$GLOBALS['fond']:_request('page');
if (!in_array($fond,$exceptions))
	$_SERVER['REQUEST_METHOD']='POST';

?>