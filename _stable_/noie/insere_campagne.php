<?php

# inserer la campagne dans la page
function noie_affichage_final(&$page) {
	// ne pas se fatiguer si pas HTML ou pas IE
	if (!($GLOBALS['html']
	AND strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie')
	AND preg_match('/MSIE /i', $_SERVER['HTTP_USER_AGENT'])
	AND !strpos('<div id="ie6msg">', $page) # pas deux fois, au cas ou !
	))
		return $page;
  
  if (!function_exists('recuperer_fond')) include_spip('public/assembler');
	return preg_replace(',<(div id=[\'"]noie[\'"]|body)\b.*?>,',
		'$0' . recuperer_fond('ie6msg', array('lang'=>$GLOBALS['spip_lang']))
		, $page);
}
