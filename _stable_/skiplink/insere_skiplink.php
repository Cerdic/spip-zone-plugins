<?php

# inserer les liens dans la page
function skiplink_affichage_final(&$page) {
	// ne pas se fatiguer si pas HTML
	if (!($GLOBALS['html']
	AND !strpos($page, '<p id="raccourcis">') # pas deux fois, au cas ou !
	))
		return $page;
  
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');
	$recherche_existe = (preg_match(',<input.*?name[ ]*=[ ]*"recherche".*?>,i', $page) == 1 ? 'oui' : 'non');
	$raccourcis = recuperer_fond('raccourcis', array('lang'=>$GLOBALS['spip_lang'], 'recherche'=>$recherche_existe));
	preg_match(',<body\b.*?>,i', $page, $regs);
  
	if ($regs)
		$page = substr_replace($page, $raccourcis, (strpos($page, $regs[0]) + strlen($regs[0])), 0);
	$remonter = recuperer_fond('remonter', array('lang'=>$GLOBALS['spip_lang']));
	$ancre_basse = recuperer_fond('ancre_basse', array('lang'=>$GLOBALS['spip_lang']));
	preg_match(',<\/body\b.*?>,i', $page, $regs);
	if ($regs)
		$page = substr_replace($page, $ancre_basse, strpos($page, $regs[0]), 0);

	return $page;
}

?>