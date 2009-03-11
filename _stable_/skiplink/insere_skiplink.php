<?php

# inserer les liens dans la page
function skiplink_affichage_final(&$page) {
	// ne pas se fatiguer si pas HTML
	if (!($GLOBALS['html']
	AND !strpos($page, '<p id="raccourcis">') # pas deux fois, au cas ou !
	))
		return $page;
  
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');
  $fond_raccourcis = (preg_match(',<input.*?name[ ]*=[ ]*"recherche".*?>,i', $page) == 1 ? 'raccourcis' : 'raccourcis_sans_recherche');
	$raccourcis = recuperer_fond($fond_raccourcis, array('lang'=>$GLOBALS['spip_lang']));
	preg_match(',<body\b.*?>,i', $page, $regs);
	if ($regs)
		$page = substr_replace($page, $raccourcis, strpos($page, $regs[0]), 0);
	$remonter = recuperer_fond('remonter', array('lang'=>$GLOBALS['spip_lang']));
	preg_match(',<\/body\b.*?>,i', $page, $regs);
	if ($regs)
		$page = substr_replace($page, $remonter, strpos($page, $regs[0]), 0);

	return $page;
}

?>