<?php

# inserer la campagne dans la page
function noie_affichage_final(&$page) {
	// ne pas se fatiguer si pas HTML ou pas IE
	if (!($GLOBALS['html']
	AND strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie')
	AND preg_match('/MSIE /i', $_SERVER['HTTP_USER_AGENT'])
	AND !strpos($page, '<div id="noie">') # pas deux fois, au cas ou !
	))
		return $page;
  
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');
	$campagne = recuperer_fond('noie', array('lang'=>$GLOBALS['spip_lang']));
	preg_match(',<div id=[\'"]noie[\'"].*?>,', $page, $regs)
	|| preg_match(',<body\b.*?>,i', $page, $regs);
	if ($regs)
		$page = substr_replace($page, $campagne, (strpos($page, $regs[0]) + strlen($regs[0])), 0);

	return $page;
}

?>