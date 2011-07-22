<?php

# inserer les liens dans la page
function skiplink_affichage_final(&$page) {
	// ne pas se fatiguer si pas HTML
	if (!$GLOBALS['html']
	  OR strpos($page, 'id="skiplinks"') # pas deux fois, au cas ou !
	)
		return $page;

	if (!function_exists('recuperer_fond')) include_spip('public/assembler');
	$recherche_existe = (preg_match(',<input.*?name[ ]*=[ ]*"recherche".*?>,i', $page) == 1 ? 'oui' : 'non');
	$skiplinks = recuperer_fond('inclure/skiplinks', array('lang'=>$GLOBALS['spip_lang'], 'recherche'=>$recherche_existe));
	preg_match(',<body\b.*?>,i', $page, $regs);
  
	if ($regs)
		$page = substr_replace($page, $skiplinks, (strpos($page, $regs[0]) + strlen($regs[0])), 0);
	$go_top = recuperer_fond('inclure/go_top', array('lang'=>$GLOBALS['spip_lang']));
	$a_bottom = recuperer_fond('inclure/a_bottom', array('lang'=>$GLOBALS['spip_lang']));
	preg_match(',<\/body\b.*?>,i', $page, $regs);
	if ($regs)
		$page = substr_replace($page, $a_bottom, strpos($page, $regs[0]), 0);

	return $page;
}

?>