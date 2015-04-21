<?php

// insert le css pour les styles supplementaires de la BTE dans le <head> du document (#INSERT_HEAD)
function nav_cef_insert_head($flux) {
	return $flux .'<script src="http://www.google.com/jsapi" type="text/javascript"></script>' . "\n" . "\n" .
	'<style type="text/css">/*<![CDATA[*/body {margin-top:24px;}/*]]>*/</style>' . "\n";
}

?>